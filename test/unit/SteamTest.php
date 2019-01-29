<?php
namespace Gt\Http\Test;

use Gt\Http\Stream;
use Gt\Http\StreamException;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class SteamTest extends TestCase {
	protected $tmpDir;
	protected $tmpFile;
	protected $tmpStream;
	protected $tmpFileFull;

	public function setUp() {
		$this->tmpDir = implode(DIRECTORY_SEPARATOR, [
			sys_get_temp_dir(),
			"phpgt",
			"http",
			"test",
		]);
		mkdir($this->tmpDir, 0775, true);
		$this->tmpFile = tempnam($this->tmpDir, "file");
		$this->tmpFileFull = tempnam($this->tmpDir, "file");
		file_put_contents($this->tmpFileFull, uniqid("data-"));
	}

	public function tearDown() {
		/** @var SplFileInfo[] $files */
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(
				$this->tmpDir,
				RecursiveDirectoryIterator::SKIP_DOTS
			),
			RecursiveIteratorIterator::CHILD_FIRST
		);
		foreach($files as $file) {
			$function = $file->isDir()
				? "rmdir"
				: "unlink";
			$function($file->getRealPath());
		}
		rmdir($this->tmpDir);
	}

	public function testToString() {
		$stream = new Stream($this->tmpFileFull);
		$contents = file_get_contents($this->tmpFileFull);
		self::assertEquals($contents, $stream);
	}

	public function testDetatch() {
		$stream = new Stream($this->tmpFile);
		$handle = $stream->getFileHandle();
		self::assertIsResource($handle);
		$detatched = $stream->detach();
		self::assertNull($stream->getFileHandle());
		self::assertSame($detatched, $handle);
	}

	public function testGetSizeIsNull() {
		$streamEmpty = new Stream($this->tmpFile);
		$streamFull = new Stream($this->tmpFileFull);
		self::assertNull($streamEmpty->getSize());
		self::assertNull($streamFull->getSize());
	}

	public function testTell() {
		$stream = new Stream($this->tmpFileFull);
		self::assertEquals(0, $stream->tell());
		$twoBytes = $stream->read(2);
		self::assertEquals(2, strlen($twoBytes));
		self::assertEquals(2, $stream->tell());
	}
}