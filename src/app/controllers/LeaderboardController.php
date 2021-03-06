<?php

use Illuminate\Database\Eloquent\Collection;

class LeaderboardController extends \BaseController {

	public function leaderboard() {
		// Get the timers
		$users = Auth::user()->follows;
		$c1 = new Collection([ Auth::user() ]);
		unset($c1[0]->follows);
		$users = $users->merge($c1);

		// $users->add(Auth::user());
		foreach($users as $user) {
			$user->seconds = $user->getSecondsThisWeek();
			$user->time = gmdate("z\d G\h i\m s\s", $user->seconds);
		}
		return Response::json($users);
	}
	
	// /**
	//  * Display a listing of the resource.
	//  *
	//  * @return Response
	//  */
	// public function index()
	// {
	// 	//
	// }


	// /**
	//  * Store a newly created resource in storage.
	//  *
	//  * @return Response
	//  */
	// public function store()
	// {
	// 	//
	// }


	// /**
	//  * Display the specified resource.
	//  *
	//  * @param  int  $id
	//  * @return Response
	//  */
	// public function show($id)
	// {
	// 	//
	// }


	// /**
	//  * Update the specified resource in storage.
	//  *
	//  * @param  int  $id
	//  * @return Response
	//  */
	// public function update($id)
	// {
	// 	//
	// }


	// /**
	//  * Remove the specified resource from storage.
	//  *
	//  * @param  int  $id
	//  * @return Response
	//  */
	// public function destroy($id)
	// {
	// 	//
	// }


}
