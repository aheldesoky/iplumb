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
    
    $("#billDate").datetimepicker({
        pickTime: false,
        language: 'ar'
    });

    $("#addcategory").on("click", function () {
        var row =   '<tr class="category-row">\n\
                        <td class="row-id"></td>\n\
                        <td>\n\
                                <input type="text" name="category" id="category" class="category-name">\n\
                                <span class="fa fa-pencil fa-fw edit-category hidden"></span>\n\
                        </td>\n\
                        <td><input type="text" name="quantity" id="quantity" class="category-value"></td>\n\
                        <td><input type="text" name="sellPrice" id="sellPrice" class="category-value"></td>\n\
                        <td><span class="fa fa-remove fa-fw remove-category"></span></td>\n\
                    </tr>';
        $('.last-row').before(row);
        $.each($('td.row-id'), function(index){
            $(this).html(index+1);
        });
    });
    
    $("#addnewcustomer").on("click", function(){
        $('#addNewCustomerModal').modal();
    })
    
    $('#addNewCustomerModal').on('hidden.bs.modal', function () {
        $('#success-msg').addClass('hidden');
        $('#customerName').val('');
        $('#customerPhone').val('');
        $('#customerNotes').val('');
        $('#customerForm .errors').remove();
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
            params: { import:false }, //aditional parameters
            noCache: true, //default is false, set to true to disable caching
            onSelect: function(category){
                var decoded = $("<div/>").html(category.value).text();
                $(this).val(decoded);
                $(this).attr('data-id',category.data)
                       .prop('readonly',true)
                       .addClass('text-center input-tagged')
                       .next().removeClass('hidden');
            }
        })
        
    }).on('click' , '.remove-category', function(){
        if($('.category-row').length > 1)
            $(this).closest('.category-row').remove();
        $.each($('td.row-id'), function(index){
            $(this).html(index+1);
        });
    }).on('click' , '.edit-category' , function(){
        $(this).prev().prop('readonly',false)
                      .removeClass('text-center input-tagged')
                      .next().addClass('hidden');
    });
    
    $('#importForm').submit(function(e){
        e.preventDefault();
        var valid = true;
        
        $('ul.errors').remove();
        $('.category-row').each(function(index, row){
            var category = {};
            category['categoryId'] = $(row).find('input[name="category"]').attr('data-id');
            category['categoryQuantity'] = $(row).find('input[name="quantity"]').val();
            category['categoryBuyPrice'] = $(row).find('input[name="buyPrice"]:enabled').val();
            category['categorySellPrice'] = $(row).find('input[name="sellPrice"]').val();
            
            if($('#billPercentage').prop('checked')){
                if(    !isInteger(category['categoryId'])
                    || !isInteger(category['categoryQuantity'])
                    || !isFloat(category['categorySellPrice'])
                    ) { valid = false; }
                category['categoryBuyPrice'] = 0;
                
            } else {
                if(    !isInteger(category['categoryId'])
                    || !isInteger(category['categoryQuantity'])
                    || !isFloat(category['categoryBuyPrice'])
                    || !isFloat(category['categorySellPrice'])
                    ) { valid = false; }
            }
            console.log(category);
            console.log(valid);
            if(!valid) return valid;
            global.categories[index] = category;
        });
        
        if(valid){
            $('#importCategories').val(JSON.stringify(global.categories));
            var importCategories = $('#importCategories').val(),
                importDiscount = ($('#billPercentage').prop('checked')) ? $('#importDiscount').val() : 0,
                importSupplier = $('#importSupplier').val(),
                importOrder = $('#importOrder').val(),
                importDate = $('#importDate').val();
                
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: global.baseUrl + '/import/add',
                async: false,
                data: { importCategories: importCategories,
                        importDiscount: importDiscount, 
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
    
    $('#importDiscount-label, #importDiscount-element').hide(); 
    $('#importDiscount').prop('disabled', true); 
    $('#billPercentage').click(function () {
        if(this.checked){ 
            $('.col-buy-price').hide(); 
            $('#importDiscount-label, #importDiscount-element').show(); 
            $('input[name="buyPrice"]').prop('disabled', true); 
            $('#importDiscount').prop('disabled', false); 
        }else{ 
            $('.col-buy-price').show(); 
            $('#importDiscount-label, #importDiscount-element').hide(); 
            $('input[name="buyPrice"]').prop('disabled', false); 
            $('#importDiscount').prop('disabled', true); 
        }
    });
    
    $('.number').attr('type', 'number')
    .on('keyup', function(){
        $(this).removeClass('errors');
        var value = $(this).val();
        
        if( $(this).hasClass('float') )
            if( !isFloat(value) ) $(this).addClass('errors');
        else
            if( !isInteger(value) ) $(this).addClass('errors');
    });
    
    function isInteger(value) { return /^\d+$/.test(value); }
    function isFloat(value)   { return /^\d+(\.\d*){0,1}$/.test(value); }
    function isPositive(value){ return (value >= 0); }
});