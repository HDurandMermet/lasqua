<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Song;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use \Google\Cloud\Firestore\Transaction as FirestoreTransaction;
use Google\Cloud\Firestore\Connection\Grpc;
use Google\Cloud\Firestore\FirestoreClient;
use App\Traits\ApiResponser;


class SongController extends Controller
{
    public function getAllSongs() {
        $songs = [];
        $db= new FirestoreClient();
        $songRefs = $db->database()->collection('Songs')->documents();
        foreach($songRefs as $songRef) {
            $songs[$songRef->id()] = $songRef->data();
        }
        return $this->success([
            'songs' => $songs
        ]);
      }
    public function getSong($song_id) {
        $request = app('firebase.firestore')->database()->collection('Songs')->document(strval($song_id))->snapshot()->data();
        if (isset($request)) {
            $song = $request;
            return response($song, 200);
        } else {
            return response()->json([
            "message" => "Song not found"
            ], 404);
        }
    }
    public function createSong(Request $request) {
        $user = $request->user();
        $songRef = app('firebase.firestore')->database()->collection('Songs')->newDocument();
        $songRef->set([
            'title' => $request->title,
            'artist' => $request->artist,
            'release_year' => $request->release_year,
            'url' => $request->url,
            'genre_id' => $request->genre_id,
            'user_id' => $user['localId']
        ]);

        return response()->json([
          "message" => "Song record created"
        ], 201);
      }
    public function updateSong(Request $request, $id) {
        $user = $request->user();
        $db = app('firebase.firestore')->database();
        $songRef = $db->collection('Songs')->document(strval($id));
        if (isset($songRef)) {
        $db->runTransaction(function (FirestoreTransaction $transaction) use ($songRef, $request, $user) {
            $snapshot = $transaction->snapshot($songRef);
            $transaction->update($songRef, [
                ['path' => 'title', 'value' => $request->title],
                ['path' => 'artist', 'value' => $request->artist],
                ['path' => 'release_year', 'value' => $request->release_year],
                ['path' => 'genre_id', 'value' => $request->genre_id],
                ['path' => 'user_id', 'value' => $user['localId']],
                ['path' => 'url', 'value' => $request->url]
            ]);
        });


        return response()->json([
        "message" => "records updated successfully"
        ], 200);
    } else {
        return response()->json([
        "message" => "Song not found"
        ], 404);
    }
    }
    public function deleteSong($id) {
/*         if(Song::where('id', $id)->exists()) {
          $song = Song::find($id);
          $song->delete(); */
          $songRef = app('firebase.firestore')->database()->collection('Songs')->document(strval($id));
          if (isset($songRef)) {
              $songRef->delete();

          return response()->json([
            "message" => "records deleted"
          ], 202);
        } else {
          return response()->json([
            "message" => "Song not found"
          ], 404);
        }
      }
}
