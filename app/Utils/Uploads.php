<?php 
namespace App\Utils;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\ApiLogService;
use App\Helper\Message;

class Uploads
{
    public static function uploadImage($file, $folder, $prefix = null)
    {
        try {
            if (!$file || !$file->isValid()) {
                ApiLogService::warning(Message::FILE_ERROR, ['file' => $file]);
                return null;
            }
            $filename = ($prefix ?? 'file') . '_' . time() .'.' . $file->getClientOriginalExtension();
            $relativePath = "uploads/{$folder}/{$filename}";
            Storage::disk('public')->putFileAs("uploads/{$folder}", $file, $filename);
            return $filename;
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return null;
        }
    }
    
    
        public static function getAddressFromCoordinates($latitude, $longitude)
        {
            $url = "https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat={$latitude}&lon={$longitude}";
        
            $headers = [
                'User-Agent: Laravel-App' // Required by Nominatim
            ];
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
            $response = curl_exec($ch);
            curl_close($ch);
        
            if ($response) {
                $data = json_decode($response, true);
                return $data['display_name'] ?? 'Address not found';
            }
        
            return 'Failed to fetch address';
        }
}


?>