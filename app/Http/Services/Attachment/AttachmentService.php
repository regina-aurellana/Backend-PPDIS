<?php

namespace App\Http\Services\Attachment;

use App\Http\Contracts\Attachment\CommunicationHasAttachment;
use App\Models\DocumentReference;
use App\Models\TemporaryFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AttachmentService implements CommunicationHasAttachment
{

    public function storeReferenceDocuments($communication, $user, $request) : string
    {

        if (is_array($request->attachments)) {

            foreach ($request->attachments as $attachment) {

                foreach ($attachment as $single_attachment) {

                    if ($single_attachment) {

                        $file_data = TemporaryFile::where('folder', $single_attachment)->first();

                        $folder = $file_data->folder;
                        $old_filename = $file_data->file;
                        $original = $file_data->original;
                        $new_filename = $old_filename;
                        $new_folder = 'communication/' . $communication->id . '/attachments/';

                        Storage::move('temp/' . $folder . '/' . $old_filename, $new_folder . $new_filename);
                        Storage::deleteDirectory('temp/' . $file_data->folder);

                        $file_data->delete();

                        $communication->referenceDocuments()->create([
                            'reference_number' => $this->generateReferenceNumber(),
                            'document_source' => $single_attachment->document_source,
                            'user_id' => $user->id,
                            'folder' => $new_folder,
                            'filename' => $new_filename,
                            'original' => $original,
                        ]);
                    }
                }
            }
        }

        return '';
    }

    public function generateReferenceNumber(): string
    {
        $counts = DocumentReference::count('id');
        $increments = str_pad(($counts + 1), 5, '0', STR_PAD_LEFT);

        return Carbon::now()->format('y') . '-' . $increments;
    }

}