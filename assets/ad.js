function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounter() {
    const count = +$('#ad_images div.form-group').length;

    $("#widgets-counter").val(count);
}

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
    
    $("#add-image").click(function() {
        // Je récupere les num des futurs champs que je vais créer
        const index = +$("#widgets-counter").val();

        //Je récupere le prototype des entrées
        const tmpl = $("#ad_images").data('prototype').replace(/__name__/g, index);  //undefined à la place de name ?
        
        //J'injecte ce code au sein de la div
        $("#ad_images").append(tmpl);

        $("#widgets-counter").val(index + 1); //Eviter le bug de l'index 

        //Je gère le boutton supprimer
        handleDeleteButtons();
    })
    updateCounter();
    handleDeleteButtons();
})