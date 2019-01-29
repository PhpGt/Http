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

	public function testEof() {
		$actualContent = file_get_contents($this->tmpFileFull);
		$stream = new Stream($this->tmpFileFull);
		$streamContent = "";

		while(!$stream->eof()) {
			$streamContent .= $stream->read(1);
		}

		self::assertEquals($actualContent, $streamContent);
	}

	public function testSeek() {
		$actualContent = file_get_contents($this->tmpFileFull);
		$stream = new Stream($this->tmpFileFull);
		self::assertTrue($stream->isSeekable());

		$offset = round(strlen($actualContent) / 2);
		$stream->seek($offset);

		$streamContent = $stream->read(1024);
		self::assertEquals(
			substr($actualContent, $offset),
			$streamContent
		);
	}

	public function testIsWriteable() {
		$stream = new Stream($this->tmpFileFull, "r");
		self::assertFalse($stream->isWritable());
		$stream = new Stream($this->tmpFileFull, "r+");
		self::assertTrue($stream->isWritable());
	}

	public function testWriteToNonWritable() {
		self::expectexceptionMessage("Stream is not writable");
		$stream = new Stream($this->tmpFile, "r");
		$stream->write("test");
	}

	public function testWrite() {
		$originalContent = file_get_contents($this->tmpFileFull);
		$newContent = uniqid("new-");
		$stream = new Stream($this->tmpFileFull);
		$stream->seek(strlen($originalContent));
		$numBytesWritten = $stream->write($newContent);
		$stream->close();
		$stream = null;

		self::assertEquals(
			$originalContent . $newContent,
			file_get_contents($this->tmpFileFull)
		);
		self::assertEquals(
			strlen($newContent),
			$numBytesWritten
		);
	}

	public function testReadNonReadable() {
		self::expectExceptionMessage("Stream is not readable");
		$stream = new Stream($this->tmpFile, "w");
		$stream->read(123);
	}

	public function testGetContentsNonReadable() {
		self::expectExceptionMessage("Stream is not readable");
		$stream = new Stream($this->tmpFile, "w");
		$stream->getContents();
	}

	public function testReadNegativeBytes() {
		self::expectExceptionMessage("Stream read length must be positive");
		$stream = new Stream($this->tmpFile);
		$stream->read(-123);
	}

	public function testGetMetaData() {
		$stream = new Stream($this->tmpFileFull);
		$metaData = $stream->getMetadata();
		$eof = $stream->getMetadata("eof");
		self::assertFalse($eof);
		self::assertArrayHasKey("eof", $metaData);
		self::assertArrayHasKey("timed_out", $metaData);
		self::assertArrayHasKey("blocked", $metaData);
		self::assertArrayHasKey("unread_bytes", $metaData);
		self::assertArrayHasKey("stream_type", $metaData);
		self::assertArrayHasKey("wrapper_type", $metaData);
		self::assertArrayHasKey("mode", $metaData);
		self::assertArrayHasKey("seekable", $metaData);
		self::assertArrayHasKey("uri", $metaData);

		$stream->read(12345);
		$eof = $stream->getMetadata("eof");
		self::assertTrue($eof);
	}
}