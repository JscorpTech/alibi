<script src="https://cdnjs.cloudflare.com/ajax/libs/imask/7.2.1/imask.min.js"></script>


<script>
    const imaskPhones = document.querySelectorAll('.imask-phone');

    imaskPhones.forEach(function (el) {
        IMask(el, {
            mask: '998 (00) 000-00-00'
        });
    });

    const imaskPrices = document.querySelectorAll(".imask-price");


    imaskPrices.forEach(function (el) {
        IMask(
            el,
            {
                mask: 'num',
                blocks: {
                    num: {
                        // nested masks are available!
                        mask: Number,
                        thousandsSeparator: ' '
                    }
                }
            }
        )
    })

</script>
