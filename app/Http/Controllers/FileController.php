<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\File;

class FileController extends Controller
{
    public function parse_csv ($csv_string, $delimiter = ",", $skip_empty_lines = true, $trim_fields = true)
    {
        return array_map(
            function ($line) use ($delimiter, $trim_fields) {
                return array_map(
                    function ($field) {
                        return str_replace('!!Q!!', '"', utf8_decode(urldecode($field)));
                    },
                    $trim_fields ? array_map('trim', explode($delimiter, $line)) : explode($delimiter, $line)
                );
            },
            preg_split(
                $skip_empty_lines ? ($trim_fields ? '/( *\R)+/s' : '/\R+/s') : '/\R/s',
                preg_replace_callback(
                    '/"(.*?)"/s',
                    function ($field) {
                        return urlencode(utf8_encode($field[1]));
                    },
                    $enc = preg_replace('/(?<!")""/', '!!Q!!', $csv_string)
                )
            )
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();
            // Create new File
            $file = new File;
            $file->url = filter_var($input['url'], FILTER_SANITIZE_URL);
            $file->slug = md5($file->url . "asd0f9834");
            $contents = file_get_contents($file->url);
            $file->contents = $contents;

            $status = 'success';
            $errors = [];
            // Parse/validate contents
            $parsed = FileController::parse_csv($contents);
            
            foreach($parsed as $line) {
                // Skip empty lines
                if($line[0] != '') {
                    // Skip comments
                    if(substr($line[0], 0, 1) != '#') {
                        // The first 3 fields are required
                        if(count($line) < 3) {
                            $status = 'error';
                            $errors[] = $line[0].' - missing a required field';
                        } else {
                            // Only 'direct' and 'reseller' are currently valid values for field 3
                            // This should be case insensitived
                            $relationship = strtolower($line[2]);
                            // Make sure to ignore comments in this field
                            $relationship = explode('#', $relationship);
                            $relationship = trim($relationship[0]);
                            
                            if($relationship != 'direct') {
                                if($relationship != 'reseller') {
                                    $status = 'error';
                                    $errors[] = $line[0].' - '.$relationship.' - is not valid';
                                }
                            }
                        }
                    }
                }
            }

            if(count($errors) > 0) {
                $file->errors = serialize($errors);
            }
            $file->status = $status;
            $file->save();
        } catch(Exception $e) {
            return redirect('/');
        }
        return redirect('/files/'.$file->slug);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        return view('file', ['entry' => File::where('slug', $slug)->latest()->first()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}