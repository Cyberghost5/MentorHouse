<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = Cache::remember('sitemap_xml', now()->addHour(), function () {
            return $this->buildXml();
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    private function buildXml(): string
    {
        $staticUrls = [
            ['loc' => url('/'),                       'changefreq' => 'daily',   'priority' => '1.0'],
            ['loc' => route('mentors.index'),         'changefreq' => 'hourly',  'priority' => '0.9'],
            ['loc' => route('login'),                 'changefreq' => 'monthly', 'priority' => '0.4'],
            ['loc' => route('register'),              'changefreq' => 'monthly', 'priority' => '0.5'],
        ];

        $mentors = User::where('role', 'mentor')
            ->where('is_active', true)
            ->with('mentorProfile')
            ->orderBy('updated_at', 'desc')
            ->get();

        $lines = ['<?xml version="1.0" encoding="UTF-8"?>'];
        $lines[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $lines[] = '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

        foreach ($staticUrls as $url) {
            $lines[] = '  <url>';
            $lines[] = '    <loc>' . e($url['loc']) . '</loc>';
            $lines[] = '    <changefreq>' . $url['changefreq'] . '</changefreq>';
            $lines[] = '    <priority>' . $url['priority'] . '</priority>';
            $lines[] = '  </url>';
        }

        foreach ($mentors as $mentor) {
            $loc        = route('mentors.show', $mentor->username);
            $lastmod    = $mentor->updated_at->toAtomString();
            $expertise  = implode(', ', array_slice($mentor->mentorProfile?->expertise ?? [], 0, 5));
            $title      = $mentor->name . ($mentor->headline ? ' — ' . $mentor->headline : '');

            $lines[] = '  <url>';
            $lines[] = '    <loc>' . e($loc) . '</loc>';
            $lines[] = '    <lastmod>' . $lastmod . '</lastmod>';
            $lines[] = '    <changefreq>weekly</changefreq>';
            $lines[] = '    <priority>0.8</priority>';

            if ($mentor->profile_photo) {
                $imgUrl = url(\Illuminate\Support\Facades\Storage::url($mentor->profile_photo));
                $lines[] = '    <image:image>';
                $lines[] = '      <image:loc>' . e($imgUrl) . '</image:loc>';
                $lines[] = '      <image:title>' . e($title) . '</image:title>';
                if ($expertise) {
                    $lines[] = '      <image:caption>' . e($expertise) . '</image:caption>';
                }
                $lines[] = '    </image:image>';
            }

            $lines[] = '  </url>';
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines);
    }
}
