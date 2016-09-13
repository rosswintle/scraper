<?php
/*
 * This script is a scraper aid that runs multiple Regular Expression patterns over
 * multiple files.
 *
 * You run the script like this:
 *
 * > php scraper.php filelist.txt patternfile.php
 *
 * The filelist is a simple text file with one file path/name on each line
 *
 * The pattern file is a php script that returns an array. For example:
 *
 * <?php
 *   return [
 *      'date' => '<span class="date-published">([^<]*)<\/span>',
 *      'title' => '<h2 class="post-title">\s*(.*)\s*<\/h2>',
 *      'content' => '<div class="post-content content-body">(.*)<\/div>',
 *    ];
 * ?>
 *
 * The script returns an array like:
 *
 * {
 *   'file1.html': {
 *     'data' : {
 *       'date' : <scraped date data>,
 *       'title' : <scraped title data>,
 *       'content' : <scraped content data>
 *    }
 *   },
 *   'file2.html' : {
 *     'data' : {
 *       'date' : <scraped date data>,
 *       'title' : <scraped title data>,
 *       'content' : <scraped content data>
 *     }
 *   }
 * }
 * 
 */

require_once('vendor/autoload.php');

class Scraper {

	static $patterns = [];

	static $files = [];

	static $results = [];

	static function scrape() {

		global $argv;

		self::$patterns = collect(self::$patterns);

		$listFilename = isset($argv[1]) ? $argv[1] : '';

		if (empty($listFilename)) {
			echo "No list file specified\n\n";
			exit;
		}

		self::$files = explode("\n", file_get_contents($listFilename));
		//var_dump(self::$files);

		if (empty(self::$files)) {
			echo "No files found\n\n";
			exit;
		}

		$patternFilename = isset($argv[2]) ? $argv[2] : '';

		if (empty($patternFilename)) {
			echo "No pattern file specified";
		}

		self::$patterns = require $patternFilename;
		self::$patterns = collect(self::$patterns);

		self::$files = collect(self::$files);

		self::$results = self::$files->mapWithKeys('Scraper::processFile');

		echo self::$results->toJson();

	}

	static function processFile( $filename ) {

		if (!empty($filename)) {
			//echo "Processing $filename\n";

			$content = file_get_contents($filename);
			return [ $filename => [ 'data' => self::runPatternsOverContents( $content )] ];
		}

	}

	static function runPatternsOverContents( &$content ) {

		return self::$patterns->mapWithKeys(function ($pattern, $variable) use ($content) {
			return self::runPattern( $pattern, $variable, $content );
		});

	}

	static function runPattern( $pattern, $variable, &$content ) {

		$result = preg_match( '/' . $pattern . '/msU', $content, $matches );
		if ( ! empty( $result ) ) {
			return [ $variable => $matches[1] ];
		} else {
			return [ $variable => '' ];
		}

	}

}
Scraper::scrape();