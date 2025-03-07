<x-filament-panels::page>
<h1 style="font-size: 25px;font-weight:700;">Subscriptions</h1>
<hr>
<pre><code>{{ json_encode(json_decode($subscription), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</code></pre>
<h1 style="font-size: 25px;font-weight:700;">Transactions</h1>
<hr>
<pre><code>{{ json_encode(json_decode($transaction), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</code></pre>
<h1 style="font-size: 25px;font-weight:700;">Contacts</h1>
<hr>
<pre><code>{{ json_encode(json_decode($contact), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</code></pre>
</x-filament-panels::page>