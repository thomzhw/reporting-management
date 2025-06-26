<?php
namespace App\Policies;

use App\Models\User;
use App\Models\QaTemplate;

class QaTemplatePolicy
{
    public function view(User $user, QaTemplate $template)
    {
        return $user->id === $template->head_id;
    }

    public function update(User $user, QaTemplate $template)
    {
        return $user->id === $template->head_id;
    }

    public function delete(User $user, QaTemplate $template)
    {
        return $user->id === $template->head_id;
    }
}