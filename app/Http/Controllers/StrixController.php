<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Services\Strix\PreviewNews;
use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class StrixController extends Controller
{
    public function getNews(Request $request, School $school)
    {
        return PreviewNews::from($school->identifier)->getNews($request->date);
    }

    public function importNews(Request $request, School $school)
    {
        $request->validate([
            'except_news_ids' => 'required|array',
            'all_posts' => 'required|boolean',
        ]);
        $exceptNewsIds = $request->get('except_news_ids');
        $getAllPosts = (bool)$request->get('all_posts');
        $newsToExport = PreviewNews::from($school->identifier)->importNews($exceptNewsIds, $request->date, $getAllPosts);

        $newsToExport->each(function ($news) use ($school){
            $process = new Process(['wp', 'post', 'create', '--url='.$school->domain, '--ssh=docker:wordpress:8081', '--post_status=publish',
                '--post_title=' . $news->title, '--post_content=' . $news->content, '--allow-root']);

//        wp post create--url = wordpress . sumit . carnet . hr--ssh = docker:wordpress:8081--post_status = publish--post_title = 'A post'--post_content = 'Just a small post.'--allow - root
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        });

        return response()->json(['success' => 'Uspješno ste uvezli novosti.']);
    }
}
