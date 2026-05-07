<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo(a) à Engenharia de Prompts</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background-color: #000000;
            color: #cbd5e1;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            line-height: 1.6;
        }
        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 32px 16px;
        }
        .card {
            background-color: #020617;
            border: 1px solid #1e293b;
            border-radius: 8px;
            overflow: hidden;
        }
        .titlebar {
            background-color: #0f172a;
            border-bottom: 1px solid #1e293b;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .dot { width: 12px; height: 12px; border-radius: 50%; display: inline-block; }
        .dot-red { background-color: #ef4444; }
        .dot-yellow { background-color: #eab308; }
        .dot-green { background-color: #10b981; }
        .filename {
            margin-left: 8px;
            font-size: 12px;
            color: #64748b;
        }
        .body {
            padding: 32px;
        }
        .header-cmd {
            color: #10b981;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 24px;
        }
        .section {
            margin-bottom: 20px;
        }
        .prompt {
            color: #10b981;
        }
        .output {
            color: #94a3b8;
            margin-left: 0;
            margin-top: 4px;
        }
        .highlight {
            color: #34d399;
        }
        .info-block {
            background-color: #0f172a;
            border: 1px solid #1e293b;
            border-left: 3px solid #10b981;
            padding: 12px 16px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .info-row {
            display: flex;
            margin-bottom: 4px;
        }
        .info-key { color: #64748b; min-width: 80px; }
        .info-val { color: #34d399; }
        .btn-block {
            text-align: center;
            margin: 28px 0;
        }
        .btn {
            display: inline-block;
            background-color: #052e16;
            border: 1px solid #14532d;
            color: #34d399;
            text-decoration: none;
            padding: 12px 32px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            border-radius: 2px;
        }
        .divider {
            border: none;
            border-top: 1px solid #1e293b;
            margin: 24px 0;
        }
        .footer {
            text-align: center;
            font-size: 11px;
            color: #334155;
            padding: 16px 32px;
            border-top: 1px solid #1e293b;
        }
        .cursor {
            display: inline-block;
            width: 8px;
            height: 14px;
            background-color: #10b981;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <!-- Titlebar -->
            <div class="titlebar">
                <span class="dot dot-red"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-green"></span>
                <span class="filename">welcome_init.sh</span>
            </div>

            <!-- Body -->
            <div class="body">
                <div class="header-cmd">&gt;_ auth --welcome {{ $userName }}</div>

                <div class="section">
                    <div class="prompt">root@prompts:~#</div>
                    <div class="output">echo "Iniciando protocolo de boas-vindas..."</div>
                </div>

                <div class="section">
                    <div class="prompt">root@prompts:~#</div>
                    <div class="output">
                        [ <span class="highlight">OK</span> ] Conta criada com sucesso.<br>
                        [ <span class="highlight">OK</span> ] Permissões de acesso configuradas.<br>
                        [ <span class="highlight">OK</span> ] Biblioteca de prompts disponível.
                    </div>
                </div>

                <!-- Info block -->
                <div class="info-block">
                    <div class="info-row">
                        <span class="info-key">user</span>
                        <span class="info-val">{{ $userName }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-key">email</span>
                        <span class="info-val">{{ $userEmail }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-key">status</span>
                        <span class="info-val">ACTIVE (pending verification)</span>
                    </div>
                    <div class="info-row">
                        <span class="info-key">access</span>
                        <span class="info-val">pending_email_verify</span>
                    </div>
                </div>

                <div class="section">
                    <div class="prompt">root@prompts:~#</div>
                    <div class="output">
                        Você receberá em instantes um email separado para<br>
                        verificar seu endereço. Após confirmá-lo, o acesso<br>
                        completo à plataforma será liberado.<br><br>
                        Seja bem-vindo(a) à <span class="highlight">Engenharia de Prompts</span> —<br>
                        onde templates de IA se tornam superpoderes.
                    </div>
                </div>

                <!-- CTA -->
                <div class="btn-block">
                    <a href="{{ $platformUrl }}" class="btn">[ &gt;_ Acessar a Plataforma ]</a>
                </div>

                <hr class="divider">

                <div class="section">
                    <div class="prompt">root@prompts:~# <span class="cursor"></span></div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                Este email foi gerado automaticamente por {{ config('app.name') }}.<br>
                Se você não criou esta conta, ignore este email com segurança.<br>
                &copy; {{ date('Y') }} Engenharia de Prompts — Todos os direitos reservados.
            </div>
        </div>
    </div>
</body>
</html>
