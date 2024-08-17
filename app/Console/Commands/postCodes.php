<?php

namespace App\Console\Commands;

use App\Models\Postcode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

ini_set('memory_limit', '2048M');

class postCodes extends Command
{
    const URL_POSTCODES_ZIP = 'https://data.freemaptools.com/download/full-uk-postcodes/ukpostcodes.zip';
    const POSTCODES_STORAGE_FOLDER = 'postcodes';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:post-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to import post codes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Downloading and extracting the postcode files
        $csvData = $this->downloadPostCodes();

        foreach ($csvData as $row) {
            $pc = preg_replace('/\s+/', '', $row['postcode']);
            $lat = trim($row['latitude']);
            $long = trim($row['longitude']);
            if (!$lat || !$long) {
                $this->warn("Postcode {$pc} missing lat: {$lat} or long: {$long}.");
                continue;
            }
            if (Postcode::where('postcode', '=', $pc)->exists()) {
                $this->info("Postcode {$pc} already exists.");
                continue;
            }
            $postcode = new Postcode();
            $postcode->postcode = $pc;
            $postcode->latitude = trim($row['latitude']);
            $postcode->longitude = trim($row['longitude']);
            $postcode->save();
            $this->info("Postcode {$postcode->postcode} added successfully.");
        }

        $this->info("------ Completed successfully! ------");
    }

    private function downloadPostCodes(): array
    {
        $url = self::URL_POSTCODES_ZIP;
        $path = self::POSTCODES_STORAGE_FOLDER;
        $extractPath = storage_path("app/{$path}");

        // Deleting all files from the folder before continue
        $this->info("Cleaning the folder: {$extractPath}");
        $files = File::files($extractPath);
        foreach ($files as $file) {
            File::delete($file);
        }
        $this->info("Cleaned the folder: {$extractPath}");

        $this->info("Downloading ZIP file from: {$url}");

        // Determine the file name from the URL
        $fileName = basename($url);
        $downloadPath = storage_path("app/{$fileName}");

        // Download the ZIP file
        $response = Http::get($url);
        if ($response->failed()) {
            $this->error('Failed to download the file.');
            return [];
        }
        $this->info("Downloaded ZIP file from: {$url}");

        // Save the ZIP file
        File::put($downloadPath, $response->body());
        $this->info("File downloaded to: {$downloadPath}");

        $this->info('Extracting ZIP file.');
        // Extract the ZIP file
        $zip = new \ZipArchive;
        if ($zip->open($downloadPath) === true) {
            $zip->extractTo($extractPath);
            $zip->close();
            $this->info("File extracted to: {$extractPath}");
        } else {
            $this->error('Failed to open the ZIP file.');
        }

        // Delete the ZIP file after extraction
        File::delete($downloadPath);
        $this->info('Deleted ZIP file.');

        // Extracting CSV Data
        $files = File::files($extractPath);
        $csvData = [];
        foreach ($files as $file) {
            $filePath = $file->getPathname();
            $this->info("");
            $csvData = array_merge($csvData, $this->extractCSVData($filePath));
        }

        return $csvData;
    }

    private function extractCSVData(string $path): array
    {
        $data = [];
        if (($handle = fopen($path, 'r')) !== false) {
            // Get the header row
            $header = fgetcsv($handle);
            while (($row = fgetcsv($handle)) !== false) {
                $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        return $data;
    }
}
