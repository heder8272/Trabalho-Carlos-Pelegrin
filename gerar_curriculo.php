<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"] ?? '';
    $telefone = $_POST["telefone"] ?? '';
    $endereco = $_POST["endereco"] ?? '';

    $dados_curriculo = [
        'dados_pessoais' => [
            'nome' => $nome,
            'email' => $email,
            'telefone' => $telefone,
            'endereco' => $endereco,
        ],
        'experiencia_profissional' => [],
        'formacao_academica' => [],
        'habilidades' => isset($_POST["habilidade"]) ? $_POST["habilidade"] : [],
        'idiomas' => [],
        'informacao_adicional' => $_POST["informacao_adicional"] ?? ''
    ];

    // Organizar os dados de experiência
    if (isset($_POST["empresa"]) && is_array($_POST["empresa"])) {
        foreach ($_POST["empresa"] as $key => $empresa) {
            $dados_curriculo['experiencia_profissional'][] = [
                'empresa' => $empresa,
                'cargo' => $_POST["cargo"][$key],
                // Outros detalhes da experiência (se houver)
            ];
        }
    }

    // Organizar os dados de formação
    if (isset($_POST["instituicao"]) && is_array($_POST["instituicao"])) {
        foreach ($_POST["instituicao"] as $key => $instituicao) {
            $dados_curriculo['formacao_academica'][] = [
                'instituicao' => $instituicao,
                'curso' => $_POST["curso"][$key],
                // Outros detalhes da formação (se houver)
            ];
        }
    }

    // Organizar os dados de idiomas
    if (isset($_POST["lingua"]) && is_array($_POST["lingua"])) {
        foreach ($_POST["lingua"] as $key => $lingua) {
            $dados_curriculo['idiomas'][] = [
                'lingua' => $lingua,
                'nivel' => $_POST["nivel"][$key],
            ];
        }
    }

    if (isset($_POST["acao"])) {
        if ($_POST["acao"] == "gerar_pdf") {
            // Lógica para gerar o PDF (como antes)
            require_once('tcpdf/tcpdf.php');

            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('Meu Gerador de Currículos');
            $pdf->SetAuthor($nome);
            $pdf->SetTitle('Currículo de ' . $nome);
            $pdf->SetSubject('Currículo');
            $pdf->SetKeywords('currículo, ' . strtolower(str_replace(' ', ',', $nome)));

            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(TRUE, 15);
            $pdf->SetFont('helvetica', '', 12);
            $pdf->AddPage();

            $html_pdf = '<h1>' . htmlspecialchars($dados_curriculo['dados_pessoais']['nome']) . '</h1>';
            $html_pdf .= '<p>Email: ' . htmlspecialchars($dados_curriculo['dados_pessoais']['email']) . '</p>';
            $html_pdf .= '<p>Telefone: ' . htmlspecialchars($dados_curriculo['dados_pessoais']['telefone']) . '</p>';
            $html_pdf .= '<p>Endereço: ' . htmlspecialchars($dados_curriculo['dados_pessoais']['endereco']) . '</p>';
            $html_pdf .= '<h2>Experiência Profissional</h2>';
            if (!empty($dados_curriculo['experiencia_profissional'])) {
                foreach ($dados_curriculo['experiencia_profissional'] as $exp) {
                    $html_pdf .= '<div><strong>' . htmlspecialchars($exp['cargo']) . '</strong> - ' . htmlspecialchars($exp['empresa']) . '</div>';
                }
            }

            $html_pdf .= '<h2>Formação Acadêmica</h2>';
            if (!empty($dados_curriculo['formacao_academica'])) {
                foreach ($dados_curriculo['formacao_academica'] as $form) {
                    $html_pdf .= '<div><strong>' . htmlspecialchars($form['curso']) . '</strong> - ' . htmlspecialchars($form['instituicao']) . '</div>';
                }
            }

            $html_pdf .= '<h2>Habilidades</h2>';
            if (!empty($dados_curriculo['habilidades'])) {
                $html_pdf .= '<ul><li>' . implode('</li><li>', array_map('htmlspecialchars', $dados_curriculo['habilidades'])) . '</li></ul>';
            }

            $html_pdf .= '<h2>Idiomas</h2>';
            if (!empty($dados_curriculo['idiomas'])) {
                $html_pdf .= '<ul>';
                foreach ($dados_curriculo['idiomas'] as $idioma) {
                    $html_pdf .= '<li>' . htmlspecialchars($idioma['lingua']) . ' - ' . htmlspecialchars($idioma['nivel']) . '</li>';
                }
                $html_pdf .= '</ul>';
            }

            if (!empty($dados_curriculo['informacao_adicional'])) {
                $html_pdf .= '<h2>Informações Adicionais</h2><p>' . htmlspecialchars($dados_curriculo['informacao_adicional']) . '</p>';
            }

            $pdf->writeHTML($html_pdf, true, false, true, false, '');
            $pdf->Output('curriculo_' . str_replace(' ', '_', strtolower($nome)) . '.pdf', 'D');
            exit();

        } elseif ($_POST["acao"] == "gerar_html") {
            // Lógica para gerar o HTML
            $html_content = '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><title>Currículo de ' . htmlspecialchars($nome) . '</title></head><body>';
            $html_content .= '<h1>' . htmlspecialchars($dados_curriculo['dados_pessoais']['nome']) . '</h1>';
            $html_content .= '<p>Email: ' . htmlspecialchars($dados_curriculo['dados_pessoais']['email']) . '</p>';
            $html_content .= '<p>Telefone: ' . htmlspecialchars($dados_curriculo['dados_pessoais']['telefone']) . '</p>';
            $html_content .= '<p>Endereço: ' . htmlspecialchars($dados_curriculo['dados_pessoais']['endereco']) . '</p>';

            $html_content .= '<h2>Experiência Profissional</h2>';
            if (!empty($dados_curriculo['experiencia_profissional'])) {
                foreach ($dados_curriculo['experiencia_profissional'] as $exp) {
                    $html_content .= '<div><strong>' . htmlspecialchars($exp['cargo']) . '</strong> - ' . htmlspecialchars($exp['empresa']) . '</div>';
                }
            }

            $html_content .= '<h2>Formação Acadêmica</h2>';
            if (!empty($dados_curriculo['formacao_academica'])) {
                foreach ($dados_curriculo['formacao_academica'] as $form) {
                    $html_content .= '<div><strong>' . htmlspecialchars($form['curso']) . '</strong> - ' . htmlspecialchars($form['instituicao']) . '</div>';
                }
            }

            $html_content .= '<h2>Habilidades</h2>';
            if (!empty($dados_curriculo['habilidades'])) {
                $html_content .= '<ul><li>' . implode('</li><li>', array_map('htmlspecialchars', $dados_curriculo['habilidades'])) . '</li></ul>';
            }

            $html_content .= '<h2>Idiomas</h2>';
            if (!empty($dados_curriculo['idiomas'])) {
                $html_content .= '<ul>';
                foreach ($dados_curriculo['idiomas'] as $idioma) {
                    $html_content .= '<li>' . htmlspecialchars($idioma['lingua']) . ' - ' . htmlspecialchars($idioma['nivel']) . '</li>';
                }
                $html_content .= '</ul>';
            }

            if (!empty($dados_curriculo['informacao_adicional'])) {
                $html_content .= '<h2>Informações Adicionais</h2><p>' . htmlspecialchars($dados_curriculo['informacao_adicional']) . '</p>';
            }

            $html_content .= '</body></html>';

            // Forçar o download do arquivo HTML
            header('Content-Type: text/html');
            header('Content-Disposition: attachment; filename="curriculo_' . str_replace(' ', '_', strtolower($nome)) . '.html"');
            echo $html_content;