<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* ==================================================================
 * Smart Accountant (المحاسب الذكي) — AI copilot configuration
 * The LLM is used ONLY as a natural-language router: it turns the
 * user question into { intent, start_date, end_date, reply }.
 * It never sees or produces figures — all numbers come from the
 * existing report models. See Welcome::copilot_ask().
 * ================================================================== */

/* Groq API key — get one free at https://console.groq.com/keys
 *
 * Read from the GROQ_API_KEY environment variable so the secret never has to
 * live in the repository. This project has no .env loader (stock CI3, and
 * ENVIRONMENT is fixed to 'production' in index.php), so set it wherever the
 * app is served:
 *     Apache   SetEnv GROQ_API_KEY "gsk_..."          (lands in $_SERVER)
 *     systemd  Environment=GROQ_API_KEY=gsk_...       (lands in getenv)
 *     Docker   -e GROQ_API_KEY=gsk_...
 *     CLI      export GROQ_API_KEY=gsk_...
 *
 * Both sources are checked because which one is populated depends on the SAPI
 * and on php.ini's variables_order.
 *
 * Falling back to the literal below is supported for machines where setting an
 * env var is impractical — but a key put there MUST NOT be committed:
 *     git update-index --skip-worktree app/config/copilot.php
 *
 * With no key at all the copilot still answers: copilotLLM() returns null and
 * Welcome::copilotFallback() routes the question by keyword instead. */
$copilot_key = getenv('GROQ_API_KEY');
if (($copilot_key === false || $copilot_key === '') && isset($_SERVER['GROQ_API_KEY'])) {
    $copilot_key = $_SERVER['GROQ_API_KEY'];
}
$config['copilot_groq_key'] = $copilot_key ?: '';
unset($copilot_key);

/* Groq model — free, OpenAI-compatible, supports JSON mode.
 * Groq retires models regularly (llama-3.3-70b-versatile was decommissioned;
 * every Llama chat model is now gone from the platform). Verify a model is
 * still listed before setting it here:
 *     curl https://api.groq.com/openai/v1/models -H "Authorization: Bearer $KEY"
 *
 * Chosen 2026-09-09 after benchmarking the router prompt against every active
 * chat model: qwen3.8-27b routed 17/17 test questions correctly (EN + AR), was
 * the fastest (~0.6s median vs the 6s timeout), gave the cleanest Arabic
 * replies, and used ~5x fewer tokens per call than gpt-oss-120b — which matters
 * against the free tier's 8k tokens/minute cap. openai/gpt-oss-120b is the
 * runner-up; openai/gpt-oss-20b was rejected for answering out-of-scope
 * requests (it wrote Python code when the prompt told it to decline). */
$config['copilot_groq_model'] = 'qwen/qwen3.8-27b';

// Tried once if the primary model is rejected (decommissioned / unknown id),
// so a retirement degrades quality instead of dropping to keyword parsing.
// Set to '' to disable.
$config['copilot_groq_model_fallback'] = 'openai/gpt-oss-120b';

// Groq chat-completions endpoint (OpenAI-compatible)
$config['copilot_groq_url'] = 'https://api.groq.com/openai/v1/chat/completions';

// Hard timeout (seconds) for the LLM call. On timeout/error the endpoint
// falls back to deterministic keyword+date parsing so it never breaks.
$config['copilot_llm_timeout'] = 6;
