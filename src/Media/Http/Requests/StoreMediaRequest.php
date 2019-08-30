<?php

namespace OptimusCMS\Media\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => [
                'required',
                'file',
                'max:'.config('media.max_file_size', 5120),
            ],
            'folder_id' => [
                'nullable',
                'exists:media_folders,id',
            ],
        ];
    }
}
