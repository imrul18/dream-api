<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\AudioArranger;
use App\Models\AudioArtist;
use App\Models\AudioComposer;
use App\Models\AudioFeaturing;
use App\Models\AudioFile;
use App\Models\AudioImage;
use App\Models\AudioProducer;
use App\Models\AudioRemixer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AudioController extends Controller
{
    public function index(Request $request)
    {
        $data = Audio::where('title', 'like', '%' . $request->q . '%');
        if (isset($request->status)) $data = $data->where('status', $request->status);
        if (isset($request->user)) $data = $data->where('user_id', $request->user);
        if (isset($request->is_coller_tune)) $data = $data->where('is_coller_tune', $request->is_coller_tune);
        $data = $data->paginate($request->get('perPage', 10));

        return response()->json($data, 200);
    }

    public function show(string $id)
    {
        $data = Audio::find($id);
        return response()->json($data, 200);
    }

    public function update(Request $request, string $id)
    {
        $res = Audio::find($id);
        $request->validate([
            'status' => 'required|in:1,2,3,4',
        ]);
        $data = $request->only([
            'status',
            'note'
        ]);
        $res->update($data);

        $message = '';
        $status = null;
        if ($request->status == 1) {
            $message = 'Audio hold successfully';
            $status = 201;
        } else if ($request->status == 2) {
            $message = 'Audio drafted successfully';
            $status = 201;
        } else if ($request->status == 3) {
            $message = 'Audio approved successfully';
            $status = 201;
        } else if ($request->status == 4) {
            $message = 'Audio rejected successfully';
            $status = 201;
        }
        return response()->json([
            'message' => $message,
            'status' => $status,
        ], $status);
    }

    //for user
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            // 'artist' => 'required',
            // 'writter' => 'required',
            // 'composer' => 'required',
            // 'main_release_date' => 'required',
            // 'original_release_date' => 'required',
            // 'language_id' => 'required',
            // 'genre_id' => 'required',
            // 'subgenre_id' => 'required',
            // 'label_id' => 'required',
            // 'format_id' => 'required',
            // 'p_line' => 'required',
            // 'c_line' => 'required',
            // 'image' => 'required',
            // 'file' => 'required',
        ]);

        $data = $request->only([
            'title',
            'subtitle',
            'writter',
            'main_release_date',
            'original_release_date',
            'language_id',
            'genre_id',
            'subgenre_id',
            'label_id',
            'format_id',
            'p_line',
            'c_line',
            'upc',
            'isrc',
            'parental_advisory_id',
            'producer_catalogue_number',
        ]);
        $data['user_id'] = auth()->user()->id; //TODO uncomment this when auth is ready

        $data['status'] = 1;
        $data['is_coller_tune'] = false;

        $audio = Audio::create($data);

        if (!$audio) {
            return response()->json([
                'message' => 'Failed to create audio',
                'status' => 203
            ], 203);
        }


        // return response()->json(["a" => $request->artist]);
        $artists = [];
        foreach ($request->artist ?? [] as $index => $artist) {
            $artists[] = [
                'audio_id' => $audio->id,
                'artist_id' => $artist['value'],
                'isPrimary' => $index == 0 ? true : false,
                'sequence_number' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        AudioArtist::insert($artists);

        $featurings = [];
        foreach ($request->featuring ?? [] as $index => $featuring) {
            $featurings[] = [
                'audio_id' => $audio->id,
                'featuring' => $featuring['name'],
                'isPrimary' => $index == 0 ? true : false,
                'sequence_number' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        AudioFeaturing::insert($featurings);

        $remixers = [];
        foreach ($request->remixer ?? [] as $index => $remixer) {
            $remixers[] = [
                'audio_id' => $audio->id,
                'remixer' => $remixer['name'],
                'isPrimary' => $index == 0 ? true : false,
                'sequence_number' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        AudioRemixer::insert($remixers);

        $arrangers = [];
        foreach ($request->arranger ?? [] as $index => $arranger) {
            $arrangers[] = [
                'audio_id' => $audio->id,
                'arranger' => $arranger['name'],
                'isPrimary' => $index == 0 ? true : false,
                'sequence_number' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        AudioArranger::insert($arrangers);

        $composers = [];
        foreach ($request->composer ?? [] as $index => $composer) {
            $composers[] = [
                'audio_id' => $audio->id,
                'composer' => $composer['name'],
                'isPrimary' => $index == 0 ? true : false,
                'sequence_number' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        AudioComposer::insert($composers);

        $producers = [];
        foreach ($request->producer ?? [] as $index => $producer) {
            $producers[] = [
                'audio_id' => $audio->id,
                'producer' => $producer['name'],
                'isPrimary' => $index == 0 ? true : false,
                'sequence_number' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        AudioProducer::insert($producers);

        $files = [];
        File::makeDirectory(public_path('uploads/file'), 0777, true, true);
        foreach ($request->file ?? [] as $index => $file) {
            return ['a' => $file['fileAudio']->getOriginalExtension()];
            $unique = uniqid();
            $fileName = $unique . time() . '.' . $file->extension();
            $destinationPath = public_path('uploads/file');
            $file->move($destinationPath, $fileName);
            $filePath = 'uploads/file/' . $fileName;

            $files[] = [
                'audio_id' => $audio->id,
                'file_name' => $fileName,
                'file_url' => $filePath,
                'sequence_number' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        AudioFile::insert($files);

        $images = [];
        File::makeDirectory(public_path('uploads/image'), 0777, true, true);
        if ($request->hasFile('image')) {
            return "A";
            $unique = uniqid();
            $fileName = $request->image->extension();
            $destinationPath = public_path('uploads/image');
            $request->image->move($destinationPath, $fileName);
            $filePath = 'uploads/image/' . $fileName;

            $images = [
                'audio_id' => $audio->id,
                'image_name' => $fileName,
                'image_url' => $filePath,
                'sequence_number' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            AudioImage::insert($images);
        }
        // foreach ($request->image ?? [] as $index => $image) {
        //     $unique = uniqid();
        //     $fileName = $unique . time() . '.' . $image->extension();
        //     $destinationPath = public_path('uploads/image');
        //     $image->move($destinationPath, $fileName);
        //     $filePath = 'uploads/image/' . $fileName;

        //     $images[] = [
        //         'audio_id' => $audio->id,
        //         'image_name' => $fileName,
        //         'image_url' => $filePath,
        //         'sequence_number' => $index + 1,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ];
        // }


        return response()->json([
            'message' => 'Audio created successfully',
            'status' => 201
        ], 201);
    }
}
