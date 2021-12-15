var RATING ={};
RATING.danhgia = function(){
  $('.star-rating a').click(function(event) {
    event.preventDefault();
    $.ajax({
      url: 'rating',
      type: 'GET',
      data: {val: $(this).attr('dt-value'),id:$('input[name=pid]').val(),table:$('input[name=table]').val()},
    })
    .done(function(data) {
      try{
        var json = JSON.parse(data);
        if(json.code!=200){
          alert(json.message);
          return;
        }
        else{
          alert("Bình chọn thành công");
        }
        var score = parseFloat(json.score); 
        var total = (json.total); 
        var s = Math.round(score * 100)/100;
        $('.star-rate').width((s*100/5)+'%');   
        $('span.average').text(s);
        $('.best').text((s*100/5)+'%');
        $('.votes').text(total);
      }
      catch(ex){
      }
    })
    .fail(function() {
      console.log("error");
    })
    .always(function() {
      console.log("complete"); 
    });
  });
}

$(function() {
  RATING.danhgia();
});