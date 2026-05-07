<?php

// app/Http/Controllers/Admin/LogController.php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $logPath = storage_path('logs/laravel.log');

        if (! file_exists($logPath)) {
            return view('dashboard.logs.index', $this->emptyPayload());
        }

        $rawLines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $rawLines = array_reverse($rawLines);

        // ── Parse every line into a structured entry ──────────────────────────
        $entries = [];
        foreach ($rawLines as $line) {
            $entry = $this->parseLine($line);
            if ($entry) {
                $entries[] = $entry;
            }
        }

        // ── Search filter (applies to all tabs) ──────────────────────────────
        $search = $request->search;
        if ($search) {
            $entries = array_filter($entries, fn ($e) =>
                str_contains(strtolower($e['message']), strtolower($search)) ||
                str_contains(strtolower($e['channel'] ?? ''), strtolower($search)) ||
                str_contains(strtolower($e['user'] ?? ''), strtolower($search))
            );
            $entries = array_values($entries);
        }

        // ── Stat counters ─────────────────────────────────────────────────────
        $totalErrors    = count(array_filter($entries, fn ($e) => in_array($e['level'], ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'])));
        $totalWarnings  = count(array_filter($entries, fn ($e) => $e['level'] === 'WARNING'));
        $totalInfo      = count(array_filter($entries, fn ($e) => $e['level'] === 'INFO'));
        $totalEntries   = count($entries);

        // ── Errors per day (last 14 days) ─────────────────────────────────────
        $errorsPerDay = $this->errorsPerDay($entries, 14);

        // ── Split into log types ──────────────────────────────────────────────
        $activityLogs = array_values(array_filter($entries, fn ($e) => $e['channel'] === 'activity'));
        $auditLogs    = array_values(array_filter($entries, fn ($e) => $e['channel'] === 'audit'));
        $apiLogs      = array_values(array_filter($entries, fn ($e) => $e['channel'] === 'api'));
        $systemLogs   = array_values(array_filter($entries, fn ($e) =>
            ! in_array($e['channel'], ['activity', 'audit', 'api'])
        ));

        return view('dashboard.logs.index', compact(
            'entries',
            'systemLogs',
            'activityLogs',
            'auditLogs',
            'apiLogs',
            'totalErrors',
            'totalWarnings',
            'totalInfo',
            'totalEntries',
            'errorsPerDay',
            'search',
        ));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Parse a single Laravel log line.
     *
     * Laravel format:
     *   [2024-05-10 14:32:01] local.ERROR: Something went wrong {"user":1,"url":"/api/v1/items",...}
     */
    private function parseLine(string $line): ?array
    {
        // Match timestamp + environment + level + message
        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+(\w+)\.(\w+):\s+(.+?)(\{.*\})?$/';

        if (! preg_match($pattern, $line, $m)) {
            return null;
        }

        [, $timestamp, $env, $level, $message, $contextJson] = array_pad($m, 6, '');

        $context = [];
        if ($contextJson) {
            $decoded = json_decode($contextJson, true);
            if (is_array($decoded)) {
                $context = $decoded;
            }
        }

        // Try to detect channel from context or message keywords
        $channel = $context['channel'] ?? $this->detectChannel($message, $context);

       $userId = $context['user'] ?? $context['user_id'] ?? null;

return [
    'timestamp' => $timestamp,
    'date'      => substr($timestamp, 0, 10),
    'env'       => $env,
    'level'     => strtoupper($level),
    'message'   => trim($message),
    'channel'   => $channel,

    'user'      => $context['user_name'] ?? ($userId ? "User #{$userId}" : null),
    'user_id'   => $userId,

    'url'       => $context['url'] ?? $context['path'] ?? null,
    'method'    => $context['method'] ?? null,
    'ip'        => $context['ip'] ?? null,
    'duration'  => $context['duration_ms'] ?? $context['duration'] ?? null,
    'status'    => $context['status'] ?? $context['response_status'] ?? null,
    'action'    => $context['action'] ?? null,
    'context'   => $context,
];
    }

    private function detectChannel(string $message, array $ctx): string
    {
        $msg = strtolower($message);

        // API channel signals
        if (
            isset($ctx['method'], $ctx['url']) ||
            str_contains($msg, 'api') ||
            str_contains($msg, 'request') ||
            str_contains($msg, 'response')
        ) {
            return 'api';
        }

        // Audit channel signals
        if (
            isset($ctx['action']) ||
            preg_match('/\b(created|updated|deleted|restored|force.deleted)\b/i', $message)
        ) {
            return 'audit';
        }

        // Activity channel signals
        if (
            isset($ctx['user']) || isset($ctx['user_id']) ||
            preg_match('/\b(login|logout|register|authenticated|failed)\b/i', $message)
        ) {
            return 'activity';
        }

        return 'system';
    }

    private function errorsPerDay(array $entries, int $days): array
    {
        $errorLevels = ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'];
        $map = [];

        // Seed last N days with 0
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $map[$date] = 0;
        }

        foreach ($entries as $e) {
            if (in_array($e['level'], $errorLevels) && isset($map[$e['date']])) {
                $map[$e['date']]++;
            }
        }

        return [
            'labels' => array_keys($map),
            'values' => array_values($map),
        ];
    }

    private function emptyPayload(): array
    {
        $labels = [];
        for ($i = 13; $i >= 0; $i--) {
            $labels[] = date('Y-m-d', strtotime("-{$i} days"));
        }

        return [
            'entries'       => [],
            'systemLogs'    => [],
            'activityLogs'  => [],
            'auditLogs'     => [],
            'apiLogs'       => [],
            'totalErrors'   => 0,
            'totalWarnings' => 0,
            'totalInfo'     => 0,
            'totalEntries'  => 0,
            'errorsPerDay'  => ['labels' => $labels, 'values' => array_fill(0, 14, 0)],
            'search'        => null,
        ];
    }
}