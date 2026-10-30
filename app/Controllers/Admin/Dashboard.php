<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CommentModel;
use App\Models\PostModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $postModel = model(PostModel::class);
        $commentModel = model(CommentModel::class);

        $totalPublished = $postModel->where('status', 'published')->countAllResults();
        $totalDrafts = $postModel->where('status', 'draft')->countAllResults();
        $totalScheduled = $postModel->where('status', 'scheduled')->countAllResults();
        $totalComments = $commentModel->countAllResults();

        $recentPosts = $postModel->orderBy('created_at', 'DESC')->findAll(6);
        $postModel->eagerLoadRelations($recentPosts);

        return view('admin/dashboard', [
            'totalPublished' => $totalPublished,
            'totalDrafts'    => $totalDrafts,
            'totalScheduled' => $totalScheduled,
            'totalComments'  => $totalComments,
            'recentPosts'    => $recentPosts,
            'user'           => auth()->user(),
        ]);
    }
}

