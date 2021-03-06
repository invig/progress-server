<?php

use Carbon\Carbon;

class Project extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

    public function user() {
        return $this->belongsTo('User');
    }
    public function watches() {
        return $this->hasMany('Watch');
    }
    public function timers() {
        return $this->hasMany('Timer');
    }
    public function progress() {
        return $this->hasMany('Progress');
    }
    public function posts() {
        return $this->hasMany('Post');
    }
    public function getIsActive() {
        $progress = $this->progress()->isRecent()->orderby('created_at', 'desc')->first();
        return !!$progress;
    }
    public function getActiveUsers() {
        // created at asc is beacuse of using users->unique
        // and it returns the last instance, not the first
        $progresses = $this->progress()->isRecent()->orderby('created_at', 'asc')->get();
        // $users = array();
        $users = new \Illuminate\Database\Eloquent\Collection;
        foreach($progresses as $progress) {
            $progress->user->onlineAt = $progress->created_at->timestamp;
            $users->add($progress->user);
        }
        return $users->unique();
    }
    public static function findForMeOrFail($id) {

        $project = Project::find($id);
        if($project->user->id !== Auth::user()->id) {
            App::abort(403, 'Not authorised');
            return;
        }
        return $project;
    }

	// Don't forget to fill this array
	protected $fillable = ['name', 'user_id', 'text'];

    public function addProgress($userId) {

        if($userId !== $this->user_id) {
            App::abort(400, 'You are not the owner for the project.');
        }

        // Get the last progress
        $lastProgress = $this->getLastProgress();
        $newProgress = new Progress;
        $newProgress->project_id = $this->id;
        $newProgress->user_id = $userId;
        $newProgress->save();

        $this->progress()->save($newProgress);
        Timer::updateProjectTimersFromProgress($this, $newProgress, $lastProgress);

        return $newProgress;
    }
    public function getLastProgress() {
        $lastProgress = $this->progress()->orderBy('created_at', 'desc')->first();   
        return $lastProgress;
    }
    public function getThisWeeksTimer() {
        return Timer::where('project_id', '=', $this->id)->where('type', '=', 'week')->where('starting', '=', Carbon::now()->startOfWeek())->first();
    }

}