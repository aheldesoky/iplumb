var global = {};

$(function() {
    $( window ).on('beforeunload', function() {
        var valid= true;
        $('.panel input[type="text"]').each(function(){
            if($(this).val() != '') valid = false;
        });
        if(!valid){
            return "سوف تفقد الوارد إذا لم تقم بالضغط أولا على زر إضافة الوارد، هل تريد حقا ترك الصفحة؟";
        }
    });
    
    global.baseUrl = $('#baseUrl').val();
    global.categories = [];
    
    $("#importDate").datetimepicker({
        pickTime: false,
        language: 'ar'
    });

    $("#addcategory").on("click", function () {
        $(this).button('loading');
        $.ajax({
            async: false,
            url: global.baseUrl + '/import/ajax',
            type: "get",
            success: function(response){
                $('.last-row').before(response);
                $.each($('td.row-id'), function(index){
                    $(this).html(index+1);
                });
                if($('#billPercentage:checked').length){
                    $('.col-buy-price').hide(); 
                    $('input[name="buyPrice"]').prop('disabled', true);
                }else{
                    $('.col-buy-price').show();
                    $('input[name="buyPrice"]').prop('disabled', false);
                }
            }
        }).always(function () {
            $('#addcategory').button('reset');
        });
    });
    
    $("#addnewcategory").on("click", function(){
        $('#addNewCategoryModal').modal();
    })
    
    $('#addNewCategoryModal').on('hidden.bs.modal', function () {
        $('#success-msg').addClass('hidden');
        $('#categoryName').val('');
        $('#categoryForm .errors').remove();
    }); 
    
    $("#addNewCategoryModal #categoryForm").on("submit", function (e) {
        e.preventDefault();
        var categoryName = $('#categoryName').val();
        $('#success-msg').addClass('hidden');
        $('#categoryForm .errors').remove();
        $('#submit').button('loading');
        $.ajax({
            async: false,
            dataType: 'json',
            url: global.baseUrl + '/category/ajax',
            type: "post",
            data: { categoryName: categoryName },
            success: function(json){
                if(json.errors){
                    $('#success-msg').addClass('hidden');
                    $.each(json.errors, function(field, msg){
                        $('#'+field).after('<ul class="errors"><li>'+msg.isEmpty+'</li></ul>');
                    });
                } else {
                    $('#success-msg').html(json.success).removeClass('hidden');
                    $('#categoryName').val('');
                }
            }
        }).always(function () {
            $('#submit').button('reset');
        });
    });
    
    $('#categoryName').autocomplete({
	serviceUrl: global.baseUrl + '/category/query',
	minChars:1,
	delimiter: /(,|;)\s*/, // regex or character
	maxHeight:400,
	zIndex: 9999,
	deferRequestBy: 0, //miliseconds
        triggerSelectOnValidInput: false,
	noCache: true, //default is false, set to true to disable caching
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
        $.each($('td.row-id'), function(index){
            $(this).html(index+1);
        });
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
            
            if(    !/^\d+$/.test(category['categoryId'])
                || !/^\d+$/.test(category['categoryQuantity'])
                || !/^\d+(\.\d*){0,1}$/.test(category['categoryBuyPrice'])
                || !/^\d+(\.\d*){0,1}$/.test(category['categorySellPrice'])
                ) valid = false;
            
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
                        $(window).unbind('beforeunload');
                        window.location.replace(json.redirectUrl);
                    }
                }
            });
            
        } else {
            $('.table-category').after('<ul class="errors"><li>يجب ملئ كل الحقول </li></ul>');
        }
    });
    
    $('#billPercentage').click(function () {
        if(this.checked){ 
            $('.col-buy-price').hide(); 
            $('input[name="buyPrice"]').prop('disabled', true); 
        }else{ 
            $('.col-buy-price').show(); 
            $('input[name="buyPrice"]').prop('disabled', false); 
        }
    });
    
});