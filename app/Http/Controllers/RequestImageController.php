<?php

namespace App\Http\Controllers;

use App\Models\RequestImage;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequestImageController extends Controller
{
    public function destroy(Request $request, RequestImage $image, AuditLogger $auditLogger)
    {
        $registryRequest = $image->registryRequest;
        $this->authorize('update', $registryRequest);

        if ($registryRequest->images()->count() <= 1) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Реклама объектида битта расм бўлиши шарт.'], 422)
                : back()->withErrors(['images' => 'Реклама объектида битта расм бўлиши шарт.']);
        }

        $old = $image->toArray();
        Storage::disk('public')->delete($image->path);
        $image->delete();

        $auditLogger->log($registryRequest, 'image_deleted', $old, [], $request);

        if ($request->expectsJson()) {
            return response()->json(['deleted' => true]);
        }

        return back()->with('success', 'Rasm o‘chirildi.');
    }
}
