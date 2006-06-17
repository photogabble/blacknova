function clean_forms()
{
    // Here we cycle through all form values (other than buy, or full), and regexp out all non-numerics. (1,000 = 1000)
    // Then, if its become a null value (type in just a, it would be a blank value. blank is bad.) we set it to zero.
    var i = document.forms[0].elements.length;
    while (i > 0)
    {
        if (form.elements[i-1].type == 'text')
        {
            var tmpval = form.elements[i-1].value.replace(/\D+/g, "");
            if (tmpval != form.elements[i-1].value)
            {
                form.elements[i-1].value = form.elements[i-1].value.replace(/\D+/g, "");
            }
        }

        if (form.elements[i-1].value == '')
        {
            form.elements[i-1].value ='0';
        }
        i--;
    }
}
