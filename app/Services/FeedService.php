<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FeedService
{
    public function getFeedFor(User $user, int $perPage = 15)
    {
        $followedIdsSubquery = Follow::query()
            ->select('followed_id')
            ->where('follower_id', $user->id);

        $groupsSubquery = DB::table('group_user')
            ->select('group_id')
            ->where('user_id', $user->id);

        $posts = Post::query()
            ->where(function ($q) use ($followedIdsSubquery, $user, $groupsSubquery) {
                $q->whereIn('user_id', $followedIdsSubquery)
                  ->orWhere('user_id', $user->id)
                  ->orWhereIn('group_id', $groupsSubquery);
            })
            ->with(['author:id,name,role,avatar_url'])
            ->withCount('likes')
            ->latest()
            ->paginate($perPage);

        $postIds = $posts->getCollection()->pluck('id');

        $commentsByPost = $this->topCommentsGroupedByPost($postIds);

        $posts->setCollection(
            $posts->getCollection()->map(function ($post) use ($commentsByPost) {
                $post->setRelation('top_comments', $commentsByPost->get($post->id, collect()));
                return $post;
            })
        );

        return $posts;
    }

    private function topCommentsGroupedByPost(Collection $postIds): Collection
    {
        if ($postIds->isEmpty()) {
            return collect();
        }

        $rankedComments = Comment::query()
            ->select([
                'comments.id',
                'comments.post_id',
                'comments.user_id',
                'comments.body',
                'comments.created_at',
            ])
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY comments.post_id ORDER BY comments.created_at DESC) as rn')
            ->whereIn('comments.post_id', $postIds);

        $rows = DB::query()
            ->fromSub($rankedComments, 'ranked_comments')
            ->where('rn', '<=', 3)
            ->orderBy('post_id')
            ->orderByDesc('created_at')
            ->get();

        $userIds = $rows->pluck('user_id')->unique()->values();
        $authors = User::query()
            ->select('id', 'name', 'role', 'avatar_url')
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id');

        return $rows->groupBy('post_id')->map(function ($comments) use ($authors) {
            return $comments->map(function ($comment) use ($authors) {
                return [
                    'id' => (int) $comment->id,
                    'body' => $comment->body,
                    'created_at' => $comment->created_at,
                    'author' => $authors[$comment->user_id] ?? null,
                ];
            })->values();
        });
    }
}
