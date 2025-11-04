<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - {{ $collectionName }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4 landscape;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: auto;
        }
        thead {
            background-color: #f8f9fa;
        }
        th {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            background-color: #f8f9fa;
        }
        td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 9px;
            vertical-align: top;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .artwork-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            display: block;
            margin: 0 auto;
        }
        .no-image {
            width: 60px;
            height: 60px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 8px;
            text-align: center;
        }
        .description {
            max-width: 200px;
            word-wrap: break-word;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Inventory Report</h1>
        <p>Collection: {{ $collectionName }} | Generated: {{ now()->format('F d, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 80px;">Image</th>
                @if($isSuperAdmin)
                <th style="width: 100px;">Company</th>
                @endif
                <th style="width: 100px;">Collection</th>
                <th style="width: 120px;">Title</th>
                <th style="width: 100px;">Artist</th>
                <th style="width: 80px;">Type</th>
                <th style="width: 60px;">Height</th>
                <th style="width: 60px;">Width</th>
                <th style="width: 50px;">Unit</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse($artworks as $artwork)
            <tr>
                <td style="text-align: center;">
                    @if($artwork['image'])
                        @php
                            // Convert relative URLs to absolute URLs for PDF
                            $imageUrl = $artwork['image'];
                            if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                                $imageUrl = url($imageUrl);
                            }
                        @endphp
                        <img src="{{ $imageUrl }}" alt="Artwork" class="artwork-image">
                    @else
                        <div class="no-image">No Image</div>
                    @endif
                </td>
                @if($isSuperAdmin)
                <td>{{ $artwork['company'] }}</td>
                @endif
                <td>{{ $artwork['collection'] }}</td>
                <td>{{ $artwork['name'] }}</td>
                <td>{{ $artwork['artist'] }}</td>
                <td>{{ $artwork['type'] }}</td>
                <td>{{ $artwork['height'] ?: '-' }}</td>
                <td>{{ $artwork['width'] ?: '-' }}</td>
                <td>{{ $artwork['unit'] }}</td>
                <td class="description">{{ $artwork['description'] ?: '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ $isSuperAdmin ? 10 : 9 }}" style="text-align: center; padding: 20px;">
                    No artworks found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Total Items: {{ count($artworks) }} | Page generated on {{ now()->format('F d, Y g:i A') }}</p>
    </div>
</body>
</html>

