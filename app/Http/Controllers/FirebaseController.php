<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use \Kreait\Firebase\Database;
class FirebaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $database = app(Database::class)->getReference('ahmed')->getValue();
        $database = app(Database::class)->getReference('ali')->set([
            "has_new_appointment" => true,
            "last_new_appointment_id" => 5
        ]);
        dd($database);
    }

}