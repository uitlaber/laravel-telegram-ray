<?php
namespace Uitlaber\LaravelTelegramRay\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class TelegramRayService
{
    protected string $botToken;
    protected string $chatId;
    protected bool $isEnabled;

    public function __construct()
    {
        $this->botToken = config('telegram-ray.bot_token');
        $this->chatId = config('telegram-ray.chat_id');
        $this->isEnabled = config('telegram-ray.enabled');
    }

    public function send(mixed $data, string $file, int $line): void
    {
        if (!$this->isEnabled || !$this->botToken || !$this->chatId) {
            return;
        }

        $formattedMessage = $this->formatMessage($data, $file, $line);

        Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
            'chat_id' => $this->chatId,
            'text' => $formattedMessage,
            'parse_mode' => 'MarkdownV2',
        ]);
    }

    protected function formatMessage(mixed $data, string $file, int $line): string
    {
        $projectName = $this->escapeMarkdown(config('app.name', 'Laravel'));

        $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file);
        $displayPath = str_replace('\\', '/', $relativePath);
        $escapedDisplayPath = $this->escapeMarkdown($displayPath);

        // --- THE FIX: Changed request()->path() to request()->fullUrl() ---
        $requestInfo = "[" . request()->method() . "] " . $this->escapeMarkdown(request()->fullUrl());

        $header = "🚀 *" . $projectName . "* \n" .
            "📄 `" . $escapedDisplayPath . ":" . $line . "` \n" .
            "📍 `" . $requestInfo . "`";

        $content = '';
        if ($data instanceof Throwable) {
            $content = "Exception: `" . $this->escapeMarkdown($data->getMessage()) . "`";
        } elseif (is_string($data) || is_numeric($data)) {
            $content = $this->escapeMarkdown($data);
        } else {
            $jsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $content = $this->escapeMarkdown($jsonContent);
        }

        return $header . "\n```json\n" . $content . "\n```";
    }

    private function escapeMarkdown(string $string): string
    {
        $chars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
        return str_replace($chars, array_map(fn($char) => '\\' . $char, $chars), $string);
    }
}