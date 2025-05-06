$(document).ready(function() {
    // Adicionar experiência profissional
    $("#addExperiencia").click(function() {
        var newItem = $(".experiencia-item:first").clone();
        newItem.find("input").val(""); // Limpar os valores dos campos clonados
        $("#experiencia").append(newItem);
    });

    // Adicionar formação acadêmica
    $("#addFormacao").click(function() {
        var newItem = $(".formacao-item:first").clone();
        newItem.find("input").val(""); // Limpar os valores
        $("#formacao").append(newItem);
    });

    // Você pode adicionar lógica para remover itens também, se desejar
});