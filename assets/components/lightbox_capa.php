<?php
/**
 * Lightbox para imagem de capa (igual ao Produtos/listar)
 * Incluir este arquivo em páginas que exibem imagens de capa clicáveis.
 * Usar class="image-capa" na img para ativar o zoom ao clicar.
 */
?>
<style>
    /* Container para o zoom da imagem */
    #image-zoom-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }

    #image-zoom-overlay.active {
        display: flex;
    }

    #image-zoom-container {
        position: relative;
        max-width: 90%;
        max-height: 90%;
    }

    #image-zoom-overlay img {
        max-width: 100%;
        max-height: 90vh;
        object-fit: contain;
        border-radius: 10px;
        box-shadow: 0 0 30px rgba(255, 255, 255, 0.3);
        animation: zoomIn 0.3s ease-out;
    }

    #close-zoom-btn {
        position: absolute;
        top: -15px;
        right: -15px;
        background-color: #fff;
        color: #333;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        transition: all 0.2s ease;
        z-index: 10000;
    }

    #close-zoom-btn:hover {
        background-color: #f44336;
        color: #fff;
        transform: scale(1.1);
    }

    @keyframes zoomIn {
        from {
            transform: scale(0.5);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .image-capa {
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .image-capa:hover {
        transform: scale(1.05);
    }
</style>
<script>
    $(document).ready(function() {
        // Criar o overlay para o zoom se não existir
        if ($('#image-zoom-overlay').length === 0) {
            $('body').append(`
                <div id="image-zoom-overlay">
                    <div id="image-zoom-container">
                        <button id="close-zoom-btn" title="Fechar">&times;</button>
                        <img src="" alt="Zoom da Imagem">
                    </div>
                </div>
            `);
        }

        // Abrir zoom ao clicar na imagem
        $(document).on('click', '.image-capa', function(e) {
            e.preventDefault();
            const imgSrc = $(this).attr('src');
            if (imgSrc) {
                $('#image-zoom-overlay img').attr('src', imgSrc);
                $('#image-zoom-overlay').addClass('active');
            }
        });

        // Fechar zoom ao clicar no botão X
        $(document).on('click', '#close-zoom-btn', function(e) {
            e.stopPropagation();
            $('#image-zoom-overlay').removeClass('active');
        });

        // Fechar zoom ao clicar fora da imagem (no fundo escuro)
        $(document).on('click', '#image-zoom-overlay', function(e) {
            if (e.target === this) {
                $(this).removeClass('active');
            }
        });

        // Fechar zoom ao pressionar ESC
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('#image-zoom-overlay').hasClass('active')) {
                $('#image-zoom-overlay').removeClass('active');
            }
        });
    });
</script>
