<?php

namespace mheap\GithubActionsReporter;

use PHPUnit\Framework\TestResult;
use PHPUnit\Framework\TestFailure;
use PHPUnit\TextUI\ResultPrinter;

class Printer extends ResultPrinter
{
	protected $currentType = null;

	protected function printHeader(): void
	{
		error_log( __CLASS__ );
		error_log( '$output= "::{$type} file={$githubWorkspace}{$file},line={$line}::{$message}"' );

	}

	protected function writeProgress(string $progress): void
	{
	}

	protected function printFooter(TestResult $result): void
	{
	}

	protected function printDefects(array $defects, string $type): void
	{
		$this->currentType = $type;

		foreach ($defects as $i => $defect) {
			$this->printDefect($defect, $i);
		}
	}

	protected function printDefectHeader(TestFailure $defect, int $count): void
	{
	}

	protected function printDefectTrace(TestFailure $defect): void
	{
		$e = $defect->thrownException();

		$errorLines = array_filter(
			explode("\n", (string)$e),
			function ($l) {
				return $l;
			}
		);

		$error = end($errorLines);
		$lineIndex = strrpos($error, ":");
		$path = substr($error, 0, $lineIndex);
		$line = substr($error, $lineIndex + 1);

		list($reflectedPath, $reflectedLine) = $this->getReflectionFromTest(
			$defect->getTestName()
		);

		if($path !== $reflectedPath) {
			$path = $reflectedPath;
			$line = $reflectedLine;
		}


//		error_log($defect->getExceptionAsString());


		$message = explode("\n", $e->getMessage());
		$message = implode( '%0A', $message );

		error_log( base64_encode( $message ));
		
		$type = $this->getCurrentType();
		$file = $this->relativePath($path);

	    $githubRepository = getenv('GITHUB_REPOSITORY');
	    $githubSha = getenv('GITHUB_SHA');
		$githubRef = getenv('GITHUB_REF');

//		::error file=/home/runner/work/bh-wp-github-actions-tests/bh-wp-github-actions-teststests/wp-mock/class-plugin-wp-mock-test.php,line=89::ok then
//		/home/runner/work/bh-wp-github-actions-tests/bh-wp-github-actions-teststests/wp-mock/class-plugin-wp-mock-test.php
		$githubWorkspace = getenv('GITHUB_WORKSPACE');

		$output = array();

		$output[] = "::{$type} file=/{$file},line={$line}::{$message}";
//		$output[] = "::{$type} file=/{$path},line={$line},col=0::{$message}";
//
//		$output[] = "::{$type} file={$githubRepository}/commit/{$githubSha}/{$file},line={$line}::{$message}";
//		$output[] = "::{$type} file={$githubRepository}/commit/{$githubSha}/{$file},line={$line},col=0::{$message}";
//
//		$output[] = "::{$type} file=/{$file},line={$line}::{$message}";
//		$output[] = "::{$type} file=/{$file},line={$line},col=0::{$message}";
//
//
//		//  home/runner/work/bh-wp-github-actions-tests/bh-wp-github-actions-teststests/wp-mock/api/class-api-mock-test.php:50
//		$output[] = "::{$type} file={$githubWorkspace}{$file},line={$line}::{$message}";
//		$output[] = "::{$type} file={$githubWorkspace}/{$file},line={$line}::{$message}";
//
//		// ::error file=BrianHenryIE/bh-wp-github-actions-teststests/wp-mock/api/class-api-mock-test.php,line=50::Failed asserting that false is true.
//		$output[] = "::{$type} file={$githubRepository}{$file},line={$line}::{$message}";
//		$output[] = "::{$type} file={$githubRepository}/{$file},line={$line}::{$message}";
//
//		$output[] = "::{$type} file={$githubSha}{$file},line={$line}::{$message}";
//		$output[] = "::{$type} file={$githubSha}/{$file},line={$line}::{$message}";
//
//		$output[] = "::{$type} file={$githubRef}{$file},line={$line}::{$message}";
//		$output[] = "::{$type} file={$githubRef}/{$file},line={$line}::{$message}";
//
//
//		$output[] = "::{$type} file={$githubWorkspace}{$file},line={$line},col=0::{$message}";
//		$output[] = "::{$type} file={$githubWorkspace}/{$file},line={$line},col=0::{$message}";
//
//		$output[] = "::{$type} file={$githubRepository}{$file},line={$line},col=0::{$message}";
//		$output[] = "::{$type} file={$githubRepository}/{$file},line={$line},col=0::{$message}";
//
//		$output[] = "::{$type} file={$githubSha}{$file},line={$line},col=0::{$message}";
//		$output[] = "::{$type} file={$githubSha}/{$file},line={$line},col=0::{$message}";
//
//		$output[] = "::{$type} file={$githubRef}{$file},line={$line},col=0::{$message}";
//		$output[] = "::{$type} file={$githubRef}/{$file},line={$line},col=0::{$message}";


//	    $output = "::{$file}: line {$line}, col 0, {$type} - {$message}";

		foreach($output as $out) {
//			error_log( base64_encode( $out ) );
			$this->write( "{$out}\n" );
//			error_log( "{$out}\n" );
		}
	}
//
//        error_log($path);
//        $message = explode("\n", $e->getMessage())[0];
//
//        $type = $this->getCurrentType();
//
//		//BrianHenryIE/bh-wp-github-actions-tests/blob/c6b8fdd3855247ddff93ce39f6b833bc9d38a971
//	    $file = "path={$this->relativePath($path)}";
//
//        $line = "line={$line}";
////	    $startline = "start_line={$line}";
////	    $endline = intval($line) + 10;
////	    $endline = "end_line={$endline}";
//        $commitpath = getenv('GITHUB_REPOSITORY' ) . '/blob/' . getenv( 'GITHUB_SHA' );
//        $annotation = "::{$type} $file,$line::{$message} {$file}\n";
//        error_log($annotation);
//        $this->write($annotation);
//    }

	protected function getCurrentType()
	{
		if (in_array($this->currentType, ['error', 'failure'])) {
			return 'error';
		}

		return 'warning';
	}

	protected function relativePath(string $path)
	{
		$relative = str_replace(getcwd() . DIRECTORY_SEPARATOR, '', $path);
		// Translate \ in to / for Windows
		$relative = str_replace('\\', '/', $relative);
		return $relative;
	}

	protected function getReflectionFromTest(string $name)
	{
		list($klass, $method) = explode('::', $name);
		$c = new \ReflectionClass($klass);
		$m = $c->getMethod($method);

		return [$m->getFileName(), $m->getEndLine()];
	}
}
