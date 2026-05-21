<?php

namespace App\Http\Controllers;

use App\Models\PortfolioProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DOMDocument;
use DOMXPath;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the portfolio projects on the frontend.
     */
    public function index()
    {
        $projects = PortfolioProject::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('portfolio.index', compact('projects'));
    }

    /**
     * Display the admin dashboard with all projects.
     */
    public function adminIndex()
    {
        $projects = PortfolioProject::orderBy('created_at', 'desc')->get();
        $stats = [
            'total' => $projects->count(),
            'published' => $projects->where('status', 'published')->count(),
            'draft' => $projects->where('status', 'draft')->count(),
        ];

        return view('admin.dashboard', compact('projects', 'stats'));
    }

    /**
     * Fetch and parse OG metadata from a URL.
     */
    public function fetchMetadata(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $url = $request->input('url');

        try {
            // Fetch HTML content with standard browser headers to prevent blocks
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
            ])->timeout(12)->get($url);

            if (!$response->successful()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ไม่สามารถเข้าถึง URL นี้ได้ (HTTP Status ' . $response->status() . ')',
                ], 400);
            }

            $html = $response->body();

            // Parse HTML
            $doc = new DOMDocument();
            // Suppress warnings caused by invalid HTML elements
            @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new DOMXPath($doc);

            // 1. Get Title
            $title = null;
            $titleNode = $xpath->query('//meta[@property="og:title"]/@content');
            if ($titleNode->length > 0) {
                $title = trim($titleNode->item(0)->nodeValue);
            } else {
                // Fallback to standard HTML title tag
                $titleNode = $xpath->query('//title');
                if ($titleNode->length > 0) {
                    $title = trim($titleNode->item(0)->nodeValue);
                }
            }

            // 2. Get Description
            $description = null;
            $descNode = $xpath->query('//meta[@property="og:description"]/@content');
            if ($descNode->length > 0) {
                $description = trim($descNode->item(0)->nodeValue);
            } else {
                // Fallback to standard meta description tag
                $descNode = $xpath->query('//meta[@name="description"]/@content');
                if ($descNode->length > 0) {
                    $description = trim($descNode->item(0)->nodeValue);
                }
            }

            // 3. Get Image URL
            $imageUrl = null;
            $imageNode = $xpath->query('//meta[@property="og:image"]/@content');
            if ($imageNode->length > 0) {
                $imageUrl = trim($imageNode->item(0)->nodeValue);
                
                // If it is a relative path, convert to absolute path
                if (!empty($imageUrl) && !preg_match('~^(?:f|ht)tps?://~i', $imageUrl)) {
                    $parsedUrl = parse_url($url);
                    $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                    if (str_starts_with($imageUrl, '/')) {
                        $imageUrl = $baseUrl . $imageUrl;
                    } else {
                        $path = isset($parsedUrl['path']) ? dirname($parsedUrl['path']) : '';
                        $imageUrl = $baseUrl . '/' . trim($path, '/') . '/' . $imageUrl;
                    }
                }
            }

            // Cleanup outputs
            $title = $title ?: parse_url($url, PHP_URL_HOST);
            $description = $description ?: 'ไม่มีคำอธิบายสำหรับลิงก์นี้';
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'original_url' => $url,
                    'title' => $title,
                    'description' => $description,
                    'image_url' => $imageUrl,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created portfolio project in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'original_url' => 'required|url',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'status' => 'required|in:published,draft',
        ]);

        try {
            PortfolioProject::create($request->only([
                'original_url',
                'title',
                'description',
                'image_url',
                'status',
            ]));

            return redirect()->route('admin.dashboard')->with('success', 'บันทึกโปรเจกต์ลง Portfolio เรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'ไม่สามารถบันทึกข้อมูลได้: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified portfolio project from the database.
     */
    public function destroy($id)
    {
        try {
            $project = PortfolioProject::findOrFail($id);
            $project->delete();

            return redirect()->route('admin.dashboard')->with('success', 'ลบโปรเจกต์ออกจากระบบสำเร็จ!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'ไม่สามารถลบข้อมูลได้: ' . $e->getMessage());
        }
    }
}
