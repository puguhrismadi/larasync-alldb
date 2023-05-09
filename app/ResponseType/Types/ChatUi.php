<?php

namespace App\ResponseType\Types;

use App\Models\Message;
use App\Models\ResponseType;
use App\Models\User;
use App\ResponseType\BaseResponseType;
use App\ResponseType\ResponseDto;
use Sundance\LarachainPromptTemplates\Prompts\PromptToken;
use Sundance\LarachainPromptTemplates\PromptTemplate;

class ChatUi extends BaseResponseType
{
    protected User $user;

    protected Message $message;

    protected ResponseType $responseType;

    protected string $content;

    public function handle(ResponseType $responseType): ResponseDto
    {
        $this->user = $this->response_dto->message->user;

        $this->content = $this->response_dto->response;

        $this->responseType = $responseType;

        $this->message = $this->response_dto->message;

        if ($this->noSystemMessage()) {
            $content = $this->getFirstQuestionPrompt();
            $this->makeSystemMessage($content->format());
        } else {
            $content = $this->makeFollowUpQuestionPrompt();
            $this->makeAssistantMessage($content->format());
        }

        return ResponseDto::from(
            [
                'message' => $this->response_dto->message->refresh(),
                'response' => null,
            ]
        );
    }

    protected function getFirstQuestionPrompt(): PromptTemplate
    {
        $template = $this->responseType->prompt_token['system'];

        $input_variables = [
            new PromptToken('context', $this->content),
        ];

        return new PromptTemplate($input_variables, $template);
    }

    protected function makeFollowUpQuestionPrompt(): PromptTemplate
    {
        $template = $this->responseType->prompt_token['assistant'];

        $input_variables = [
            new PromptToken('context', $this->content),
            new PromptToken('question', $this->message->content),
        ];

        return new PromptTemplate($input_variables, $template);
    }

    protected function makeSystemMessage(string $content): void
    {
        Message::create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'content' => $content,
            'role' => 'system',
        ]);
    }

    protected function makeAssistantMessage(string $content): void
    {
        Message::create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'content' => $content,
            'role' => 'assistant',
        ]);
    }

    private function noSystemMessage(): bool
    {
        return ! Message::query()
            ->select(['role', 'content'])
            ->where('user_id', $this->user->id)
            ->where('project_id', $this->project->id)
            ->where('role', 'system')
            ->exists();
    }
}
