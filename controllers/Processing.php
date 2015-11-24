<?php namespace IDesigning\PostProxy\Controllers;

use Event;
use IDesigning\PostProxy\Models\Channel;
use IDesigning\PostProxy\Models\Recipient;
use IDesigning\PostProxy\Models\Rubric;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Processing extends Controller
{
    public function postSubscribe(Request $request)
    {
        $email = filter_var($request->get('email'), FILTER_VALIDATE_EMAIL);
        $name = $request->get('name');
        $rubrics = $request->get('rubrics');
        if ($email && $rubrics) {
            foreach ($rubrics as $rubricSlug) {
                $rubric = Rubric::whereSlug($rubricSlug)->first();
                if ($rubric) {
                    $recipient = Recipient::whereEmail($email)->first();
                    if ($recipient == null) {
                        $recipient = Recipient::create([
                            'email' => $email,
                            'name' => $name,
                            'comment' => 'Добавился через рубрику «' . $rubric->name . '»',
                        ]);
                    }
                    $exists = $rubric->recipients()->whereRecipientId($recipient->id)->first();
                    if ($exists == null) {
                        $rubric->recipients()->attach($recipient);
                        Event::fire('idesigning.postproxy.subscribe',
                            [ 'recipient' => $recipient, 'rubric' => $rubric ]);
                    }
                }
            }
        }

        return redirect()->intended('/');
    }

    public function getUnsubscribe(Request $request)
    {
        $channelId = (int)$request->get('channel_id');
        $recipient = Recipient::whereEmail($request->get('email'))->first();
        $rubrics = $request->get('rubrics');

        if ($recipient) {
            if ($channelId != 0) {
                $channel = Channel::find($channelId);
                if ($channel) {
                    $item = $channel->recipients()->whereRecipientId($recipient->id)->first();
                    if ($item) {
                        $item->pivot->update([ 'is_unsubscribed' => true ]);
                        Event::fire('idesigning.postproxy.unsubscribe.channel',
                            [ 'recipient' => $recipient, 'channel' => $channel ]);
                    }
                }
            }

            if ($rubrics != null) {
                if (is_array($rubrics) == false) {
                    $rubrics = [ $rubrics ];
                }
                foreach ($rubrics as $rubricSlug) {
                    $rubric = Rubric::whereSlug($rubricSlug)->first();
                    if ($rubric) {
                        $item = $rubric->recipients()->whereRecipientId($recipient->id)->first();
                        if ($item) {
                            $item->pivot->update([ 'is_unsubscribed' => true ]);
                            Event::fire('idesigning.postproxy.unsubscribe.rubric',
                                [ 'recipient' => $recipient, 'rubric' => $rubric ]);
                        }
                    }
                }
            }
        }

        return redirect()->intended('/');
    }
}