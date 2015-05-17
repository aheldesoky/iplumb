var global = {};

$(function() {
    global.baseUrl = $('#baseUrl').val();
    global.categories = [];
    
    $.datepicker.setDefaults( $.datepicker.regional[ "ar" ] );

    $( "#importDate" ).datepicker({
        dateFormat: "yy-mm-dd"
    });

    $("#addcategory").on("click", function () {
        //$('#addcategory').button('loading');
        $.ajax({
            async: false,
            url: global.baseUrl + '/import/ajax',
            type: "get",
            success: function(response){
                $('.last-row').before(response);
            }
        }).always(function () {
            //$('#addcategory').button('reset');
        });
    });

    $('.table-category').on('click', 'input[name="category"]', function(){
        $(this).autocomplete({
            serviceUrl: global.baseUrl + '/category/query',
            minChars:1,
            delimiter: /(,|;)\s*/, // regex or character
            maxHeight:400,
            //width:300,
            zIndex: 9999,
            deferRequestBy: 0, //miliseconds
            triggerSelectOnValidInput: false,
            params: { import:true }, //aditional parameters
            noCache: true, //default is false, set to true to disable caching
            onSelect: function(category){
                var decoded = $("<div/>").html(category.value).text();
                $(this).val(decoded);
                $(this).attr('data-id',category.data);
            }
        })
        
    }).on('click' , '.remove-category', function(){
        if($('.category-row').length > 1)
            $(this).closest('.category-row').remove();
    });
    
    $('#importForm').submit(function(e){
        e.preventDefault();
        var valid = true;
        
        $('ul.errors').remove();
        $('.category-row').each(function(index, row){
            var category = {};
            category['categoryId'] = $(row).find('input[name="category"]').attr('data-id');
            category['categoryQuantity'] = $(row).find('input[name="quantity"]').val();
            category['categoryBuyPrice'] = $(row).find('input[name="buyPrice"]').val();
            category['categorySellPrice'] = $(row).find('input[name="sellPrice"]').val();
            $.each(category, function(i,value){
                var isnum = /^\d+$/.test(value);
                if(!isnum){ valid=false; return valid; }
            });
            if(!valid) return valid;
            global.categories[index] = category;
        });
        
        if(valid){
            $('#importCategories').val(JSON.stringify(global.categories));
            var importCategories = $('#importCategories').val(),
                importSupplier = $('#importSupplier').val(),
                importOrder = $('#importOrder').val(),
                importDate = $('#importDate').val();
                
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: global.baseUrl + '/import/add',
                async: false,
                data: { importCategories: importCategories, 
                        importSupplier: importSupplier, 
                        importOrder: importOrder, 
                        importDate: importDate },
                success: function(json) {
                    if(json.errors){
                        $.each(json.errors, function(field, msg){
                            $('#'+field).after('<ul class="errors"><li>'+msg.isEmpty+'</li></ul>');
                        });
                        
                    } else {
                        window.location.replace(json.redirectUrl);
                    }
                }
            });
            
        } else {
            $('.table-category').after('<ul class="errors"><li>يجب ملئ كل الحقول بأرقام</li></ul>');
        }
    });
});