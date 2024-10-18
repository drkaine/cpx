<?php

namespace Cpx\Tests\Unit;

use Cpx\Package;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class PackageTest extends TestCase
{
    public static function invalidPackageName(): array
    {
        return [
            ['', 'A package name must be provided.'],
            ['test', 'A package name should be in the format "<vendor>/<package>'],
        ];
    }

    #[DataProvider('invalidPackageName')]
    public function testParseInvalidPackageName(string $packageName, string $expectedExceptionMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        Package::parse($packageName);
    }

    public static function validPackageName(): array
    {
        return [
            ['test/test', 'test', 'test', null],
            ['test/test@1.0.0', 'test', 'test', '1.0.0'],
        ];
    }

    #[DataProvider('validPackageName')]
    public function testParseValidPackageName(string $packageName, string $vendor, string $name, ?string $version): void
    {
        $package = Package::parse($packageName);

        $this->assertEquals($vendor, $package->vendor);
        $this->assertEquals($name, $package->name);
        $this->assertEquals($version, $package->version);
    }

    public static function differentVersionName(): array
    {
        return [
            ['test/test', 'latest'],
            ['test/test@1.0.0', '1.0.0'],
        ];
    }

    #[DataProvider('differentVersionName')]
    public function testVersionName(string $packageName, string $expectedVersionName): void
    {
        $package = Package::parse($packageName);

        $this->assertEquals($expectedVersionName, $package->versionName());
    }

    public static function differentFolder(): array
    {
        return [
            ['test/test', 'test/test/latest'],
            ['test/test@1.0.0', 'test/test/1.0.0'],
        ];
    }

    #[DataProvider('differentFolder')]
    public function testFolder(string $packageName, string $expectedFolder): void
    {
        $package = Package::parse($packageName);

        $this->assertEquals($expectedFolder, $package->folder());
    }

    public static function differentFullPackageString(): array
    {
        return [
            ['test/test', 'test/test'],
            ['test/test@1.0.0', 'test/test:1.0.0'],
        ];
    }

    #[DataProvider('differentFullPackageString')]
    public function testFullPackageString(string $packageName, string $expectedFullPackageString): void
    {
        $package = Package::parse($packageName);

        $this->assertEquals($expectedFullPackageString, $package->fullPackageString());
    }

    #[DataProvider('differentFullPackageString')]
    public function tesToString(string $packageName, string $expectedFullPackageString): void
    {
        $package = Package::parse($packageName);

        $this->assertEquals($expectedFullPackageString, $package->__toString());
    }

    #[DataProvider('differentFullPackageString')]
    public function testDelete(string $packageName): void
    {
        $package = Package::parse($packageName);
        $packageDirectory = cpx_path("{$package->folder()}");

        mkdir($packageDirectory, 0755, true);

        $this->assertDirectoryExists($packageDirectory);

        $package->delete();

        clearstatcache(true);

        $this->assertFalse(file_exists($packageDirectory));
        $this->assertDirectoryDoesNotExist($packageDirectory);

        $directory = cpx_path('test');
        exec("rm -rf {$directory}");
    }

    public function testShouldCheckForUpdates(): void
    {
        $package = Package::parse('test/test');
        $this->assertTrue($package->shouldCheckForUpdates());
    }

    // need to test runCommand and installOrUpdatePackage and shouldCheckForUpdates who return false
}
