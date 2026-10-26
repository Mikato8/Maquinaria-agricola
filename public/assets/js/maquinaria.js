(function () {
    'use strict';

    // Cierra el submenú de herramientas al hacer clic fuera.
    document.addEventListener('click', function (event) {
        document.querySelectorAll('details.nav-group[open]').forEach(function (group) {
            if (!group.contains(event.target)) {
                group.removeAttribute('open');
            }
        });
    });

    // Comparador: máximo 3 máquinas seleccionadas.
    document.querySelectorAll('[data-compare-form]').forEach(function (form) {
        var boxes = form.querySelectorAll('input[name="ids[]"]');
        var counter = form.querySelector('[data-compare-count]');
        form.addEventListener('change', function () {
            var checked = Array.prototype.filter.call(boxes, function (box) { return box.checked; });
            if (checked.length > 3) {
                checked[checked.length - 1].checked = false;
                checked = checked.slice(0, 3);
            }
            if (counter) {
                counter.textContent = checked.length + ' de 3';
            }
        });
    });

    // Botón de impresión de planes de mantenimiento.
    document.querySelectorAll('[data-print]').forEach(function (button) {
        button.addEventListener('click', function () { window.print(); });
    });

    // Rango de potencia: sincroniza el valor visible.
    document.querySelectorAll('[data-range-output]').forEach(function (input) {
        var output = document.getElementById(input.getAttribute('data-range-output'));
        if (!output) { return; }
        var sync = function () { output.textContent = input.value + ' HP'; };
        input.addEventListener('input', sync);
        sync();
    });

    // Selección de tractor en la calculadora de compatibilidad.
    document.querySelectorAll('[data-tractor-select]').forEach(function (select) {
        select.addEventListener('change', function () {
            var option = select.options[select.selectedIndex];
            if (!option) { return; }
            var map = {
                'data-hp': 'tractor_power_hp',
                'data-lift': 'tractor_lift_kg',
                'data-flow': 'tractor_flow_lpm',
                'data-weight': 'tractor_weight_kg',
                'data-traction': 'tractor_traction'
            };
            Object.keys(map).forEach(function (attr) {
                var field = document.querySelector('[name="' + map[attr] + '"]');
                var value = option.getAttribute(attr);
                if (field && value) { field.value = value; }
            });
        });
    });
}());
