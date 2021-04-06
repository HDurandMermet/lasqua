<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Google\Cloud\Firestore\Transaction as FirestoreTransaction;

class ChatroomController extends Controller
{
    public function getAllMsgs($room) {
        $msgs = [];
        $msgRefs = app('firebase.firestore')->database()->collection(strval($room))->documents();
        foreach($msgRefs as $msgRef) {
            $msgs[$msgRef->id()] = $msgRef->data();
        }
        return response($msgs, 200);
      }
    public function getMsg($room, $msg_id) {
        $request = app('firebase.firestore')->database()->collection(strval($room))->document(strval($msg_id))->snapshot()->data();
        if (isset($request)) {
            $msg = $request;
            return response($msg, 200);
        } else {
            return response()->json([
            "message" => "Msg not found"
            ], 404);
        }
    }
    public function createMsg(Request $request, $room) {
        $auth = app('firebase.auth');
        $user = $request->user();
        $msgRef = app('firebase.firestore')->database()->collection(strval($room))->newDocument();
        $msgRef->set([
            'text' => $request->text,
            'userName' => $user['displayName'],
            'userId' => $user['localId'],
            'userPhotoURL' => "https://lh5.googleusercontent.com/-hl7NxDWizyg/AAAAAAAAAAI/AAAAAAAAAAA/AMZuuckUqO68Ro71ApoHoudx0gmtnBDSuQ/s96-c/photo.jpg"
        ]);

        return response()->json([
          "message" => "Message record created"
        ], 201);
      }
    public function updateMsg(Request $request, $room, $id) {
        $db = app('firebase.firestore')->database();
        $msgRef = $db->collection(strval($room))->document(strval($id));
        if (isset($msgRef)) {
        $db->runTransaction(function (FirestoreTransaction $transaction) use ($msgRef, $request) {
            $snapshot = $transaction->snapshot($msgRef);
            $transaction->update($msgRef, [
                ['path' => 'text', 'value' => $request->text]
            ]);
        });


        return response()->json([
        "message" => "records updated successfully"
        ], 200);
    } else {
        return response()->json([
        "message" => "Message not found"
        ], 404);
    }
    }
    public function deleteSong($room, $id) {
/*         if(Song::where('id', $id)->exists()) {
          $song = Song::find($id);
          $song->delete(); */
          $msgRef = app('firebase.firestore')->database()->collection(strval($room))->document(strval($id));
          if (isset($msgRef)) {
              $msgRef->delete();

          return response()->json([
            "message" => "records deleted"
          ], 202);
        } else {
          return response()->json([
            "message" => "Msg not found"
          ], 404);
        }
      }
}
