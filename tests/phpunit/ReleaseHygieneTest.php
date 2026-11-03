<?php
/**
 * Release hygiene: catches the mistakes that only show up after a release.
 *
 * - version strings out of sync
 * - a readme WordPress.org rejects
 * - development/test files leaking into the WordPress.org package
 *
 * @package ASKORA
 */

class ReleaseHygieneTest extends WP_UnitTestCase {

	private function root(): string {
		return dirname( __DIR__, 2 );
	}

	private function read( string $relative ): string {
		$content = file_get_contents( $this->root() . '/' . $relative );
		$this->assertNotFalse( $content, "Cannot read $relative" );
		return (string) $content;
	}

	private function readme_field( string $field ): string {
		preg_match( '/^' . preg_quote( $field, '/' ) . ':\s*(.+)$/mi', $this->read( 'readme.txt' ), $m );
		return isset( $m[1] ) ? trim( $m[1] ) : '';
	}

	public function test_all_version_strings_agree() {
		preg_match( '/^\s*\*\s*Version:\s*(\S+)/m', $this->read( 'askora-community-qa.php' ), $header );
		$version = $header[1] ?? '';

		$this->assertMatchesRegularExpression( '/^\d+\.\d+\.\d+$/', $version, 'Plugin header version looks malformed.' );
		$this->assertSame( $version, ASKORA_VERSION, 'ASKORA_VERSION differs from the plugin header.' );
		$this->assertSame( $version, $this->readme_field( 'Stable tag' ), 'readme.txt Stable tag differs from the plugin header.' );
		$this->assertSame( $version, json_decode( $this->read( 'package.json' ), true )['version'], 'package.json version differs from the plugin header.' );
	}

	public function test_readme_has_a_changelog_and_upgrade_notice_for_the_current_version() {
		$readme = $this->read( 'readme.txt' );

		$this->assertMatchesRegularExpression( '/^= ' . preg_quote( ASKORA_VERSION, '/' ) . ' =$/m', $readme, 'No changelog entry for the current version.' );
		$this->assertSame( 2, preg_match_all( '/^= ' . preg_quote( ASKORA_VERSION, '/' ) . ' =$/m', $readme ), 'Expected one Changelog and one Upgrade Notice heading for the current version.' );
	}

	public function test_tested_up_to_uses_the_major_minor_form_wordpress_org_requires() {
		$this->assertMatchesRegularExpression( '/^\d+\.\d+$/', $this->readme_field( 'Tested up to' ), '"Tested up to" must be major.minor (for example 7.1), not 7.1.4.' );
	}

	public function test_readme_requirements_match_the_plugin_header() {
		$header = $this->read( 'askora-community-qa.php' );
		preg_match( '/Requires at least:\s*(\S+)/', $header, $wp );
		preg_match( '/Requires PHP:\s*(\S+)/', $header, $php );

		$this->assertSame( $wp[1], $this->readme_field( 'Requires at least' ) );
		$this->assertSame( $php[1], $this->readme_field( 'Requires PHP' ) );
	}

	public function test_readme_mentions_no_files_that_do_not_exist() {
		$this->assertDirectoryDoesNotExist( $this->root() . '/languages', 'languages/ does not exist, so the readme must not promise a .pot file.' );
		$this->assertStringNotContainsString( '.pot', $this->read( 'readme.txt' ) );
	}

	/**
	 * @return array<string, array{0:string}>
	 */
	public function packaging_exclusions(): array {
		$entries = [ 'tests', 'bin', 'phpunit.xml.dist', '.github', '.distignore', '.claude', '.cursor', '.gemini', '.opencode', 'docs', 'node_modules', 'composer.lock', 'package.json', 'README.md', '.wordpress-org' ];
		return array_combine( $entries, array_map( static fn( $e ) => [ $e ], $entries ) );
	}

	/**
	 * Everything in .distignore is excluded from the WordPress.org package by the deploy action.
	 *
	 * @dataProvider packaging_exclusions
	 */
	public function test_development_files_are_excluded_from_the_wordpress_org_package( string $entry ) {
		$lines = array_map( 'trim', explode( "\n", $this->read( '.distignore' ) ) );
		$this->assertContains( $entry, $lines, "$entry must be listed in .distignore or it ships to WordPress.org." );
	}

	public function test_runtime_directories_are_not_excluded_from_the_package() {
		$lines = array_map( 'trim', explode( "\n", $this->read( '.distignore' ) ) );
		foreach ( [ 'Admin', 'Frontend', 'Inc', 'assets', 'readme.txt', 'uninstall.php', 'askora-community-qa.php', 'composer.json', 'vendor' ] as $needed ) {
			$this->assertNotContains( $needed, $lines, "$needed is needed at runtime and must not be in .distignore." );
		}
	}

	public function test_package_built_with_distignore_contains_no_test_files() {
		if ( ! function_exists( 'shell_exec' ) || '' === trim( (string) shell_exec( 'command -v rsync' ) ) ) {
			$this->markTestSkipped( 'rsync is not available.' );
		}

		// Same invocation as 10up/action-wordpress-plugin-deploy.
		$dest = sys_get_temp_dir() . '/askora-package-' . wp_generate_password( 8, false );
		mkdir( $dest );
		$cmd = sprintf( 'rsync -rc --exclude-from=%s %s/ %s/ 2>&1', escapeshellarg( $this->root() . '/.distignore' ), escapeshellarg( $this->root() ), escapeshellarg( $dest ) );
		shell_exec( $cmd );

		$shipped = [];
		$iter    = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dest, FilesystemIterator::SKIP_DOTS ) );
		foreach ( $iter as $file ) {
			$shipped[] = ltrim( str_replace( $dest, '', $file->getPathname() ), '/' );
		}
		shell_exec( 'rm -rf ' . escapeshellarg( $dest ) );

		$this->assertContains( 'askora-community-qa.php', $shipped, 'The simulated package is empty: rsync did not run.' );
		$this->assertContains( 'readme.txt', $shipped );
		$this->assertContains( 'Frontend/Inc/Helpers/Editor.php', $shipped );

		// vendor/ is deliberately not checked here: this suite runs with dev dependencies installed,
		// whereas the deploy workflow installs with --no-dev and re-checks the package itself.
		$leaked = array_values(
			array_filter(
				$shipped,
				static function ( $path ) {
					return (bool) preg_match( '#^(tests/|bin/|\.github/|docs/)#', $path )
						|| preg_match( '#(^|/)(phpunit\.xml(\.dist)?|\.phpunit\.result\.cache|install-wp-tests\.sh)$#', $path );
				}
			)
		);
		$this->assertSame( [], $leaked, "These development files would be published to WordPress.org:\n" . implode( "\n", $leaked ) );
	}
}
