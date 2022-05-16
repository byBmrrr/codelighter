/*
 * Copyright (C) 2022 boomer
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */



/*
 *  *******************************************************************************
 * Create code element for copying test code
 *  *******************************************************************************
 */
const codeField = document.getElementById('code-test');
let targetTag = document.querySelector('#cl-preview-code');
let wrapperTargetTagNew = document.createElement('pre');
wrapperTargetTagNew.setAttribute('id', 'cl-preview-code');
let targetTagNew = document.createElement('code');
wrapperTargetTagNew.append(targetTagNew);
targetTagNew.innerHTML = targetTag.querySelector('code').innerHTML;
targetTag.after(wrapperTargetTagNew);
targetTag.remove();
hljs.highlightAll();
hljs.configure({
    ignoreUnescapedHTML: 'true',
});

/*
 *  *******************************************************************************
 * Link formation for choosed style
 *  *******************************************************************************
 */
let clSelectTag = document.querySelector('#codelighter-option-style');
const getHlScript = (e) => {
    let clStyle = document.querySelector('#highlight-css');
    let clPath = clStyle.href.split('/');
    let clName = clPath[clPath.length - 1].split('.');
    let clOptionSelected = e.target.options[e.target.selectedIndex];
    //    let clSelOptValue = clOptionSelected.value;
    clName[0] = clOptionSelected.value;
    clName = clName.join('.');
    clPath[clPath.length - 1] = clName;
    clPath = clPath.join('/');
    //    clStyle.href = clPath;
    clStyle2 = document.createElement('link');
    clStyle2.rel = 'stylesheet';
    clStyle2.id = 'highlight-css';
    wrapperTargetTagNew.classList.add('loading');
    clStyle2.href = clPath;
    clStyle2.addEventListener('load', () => {
        checkCopyColor();
        console.log('%c%s %cstyle was loaded', 'color: green; font: 1rem/1 Tahoma;', clOptionSelected.value, '');
        wrapperTargetTagNew.classList.remove('loading');
    });
    clStyle2.addEventListener('error', () => {
        console.error(clOptionSelected.value, 'style was not loaded');
        wrapperTargetTagNew.classList.remove('loading');
    });
    clStyle.remove();
    document.head.append(clStyle2);
};

clSelectTag.addEventListener('change', getHlScript);

/*
 *  *******************************************************************************
 * Copy text color to input if there not correct
 *  *******************************************************************************
 */
const checkCopyColor = () => {
    let hljsBlockStyles = getComputedStyle(document.querySelector('.hljs '));
    let hljsBlockStylesColor = hljsBlockStyles.color;
    document.getElementById('codelighter_option_selected_color').value = RGBToHex(hljsBlockStylesColor); //convert rgb color to hex and assign as value of input type=color
    console.log('Text color in choose theme is %s', hljsBlockStylesColor);
}

let saveButton = document.getElementById('submit');
saveButton.addEventListener('mouseenter', checkCopyColor);

/*
 *  *******************************************************************************
 * AJAX call for check what post types checked
 *  *******************************************************************************
 */
const fetchBody = {
    action: 'cl_get_checked_post_types',
}
const fetchOptions = {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8',
    },
    body: new URLSearchParams(fetchBody),
}


fetch(ajaxurl, fetchOptions)
    .then(response => response.json())
    .then(result => {
        let resultArray = Object.values(result);
        console.log('%cChecked post types: %s', 'color: green; Tahoma;', resultArray.join(', '));
        // let inputs = document.querySelectorAll('input[id^=codelighter_option_post_types-]');
        // console.log(inputs);
        let tmpInpt;
        resultArray.forEach(item => {
            tmpInpt = `input[id=codelighter_option_post_types-${item}]`;
            if (document.querySelector(tmpInpt)) {
                document.querySelector(tmpInpt).checked = true;
            }
        })
        let checkboxes = document.querySelectorAll('input[id*=codelighter_option_post_types-]');
        let checkboxCheked = true;
        for (let i = 0; i<checkboxes.length; i++) {
            if (!checkboxes[i].checked) {
                checkboxCheked = false;
                break;
            }
        }
        if (checkboxCheked) {
            document.querySelector('.check-all span').classList.add('checked');
        }
    })
    .catch(function (err) {
        console.error(err);
    });


/*
 *  *******************************************************************************
 * Button for check all checkbox with post types
 *  *******************************************************************************
 */
let checkCheckbox = (spanElem) => {
    spanElem.classList.toggle('checked');
    let checked = spanElem.classList.contains('checked');
    let postTypes = document.querySelectorAll('.codelighter_post_type');
    if (checked) {
        postTypes.forEach(item => {
            item.checked = 'true';
        })
    } else {
        postTypes.forEach(item => {
            item.checked = '';
        })
    }
}

let checkPostTypes = () => {
    let pElem = document.createElement('p');
    pElem.classList.add('check-all');
    let pElemSpan = document.createElement('span');
    pElem.insertAdjacentElement("afterbegin", pElemSpan);
    pElem.insertAdjacentText("beforeend", 'Check all');
    let legendElem = document.querySelector('.check-post-types');
    legendElem.insertAdjacentElement("afterend", pElem);
    pElem.addEventListener('click', function (e) {
        if (e.target.matches('span')) {
            checkCheckbox(e.target);
        } else {
            checkCheckbox(e.target.querySelector('span'));
        }
        
    })
};
checkPostTypes();


/*
 *  *******************************************************************************
 * HTML character escape
 *  *******************************************************************************
 */
const escapeHTML = str =>
    str.replace(
        /[&<>'"]/g,
        tag =>
        ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#39;',
            '"': '&quot;'
        } [tag] || tag)
    );



/*
 *  *******************************************************************************
 * Function for copy test code in tag <code>
 *  *******************************************************************************
 */
const copyCode = (eventTarget) => {
    wrapperTargetTagNew.classList.add('loading');
    targetTagNew.innerHTML = escapeHTML(eventTarget.value);
    targetTagNew.className = '';
    hljs.highlightElement(targetTagNew);
    setTimeout(() => wrapperTargetTagNew.classList.remove('loading'), 300);
};

/*
 *  *******************************************************************************
 * Event and timeout function for copy test code
 *  *******************************************************************************
 */
let timeoutId;
const highLightCode = (e) => {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(copyCode, 1000, e.target);
};

codeField.addEventListener('input', highLightCode);

/*
 *  *******************************************************************************
 * Convert RGB color to HEX
 *  *******************************************************************************
 */
function RGBToHex(rgb) {
    // Choose correct separator
    let sep = rgb.indexOf(",") > -1 ? "," : " ";
    // Turn "rgb(r,g,b)" into [r,g,b]
    rgb = rgb.substr(4).split(")")[0].split(sep);

    let r = (+rgb[0]).toString(16),
        g = (+rgb[1]).toString(16),
        b = (+rgb[2]).toString(16);

    if (r.length == 1)
        r = "0" + r;
    if (g.length == 1)
        g = "0" + g;
    if (b.length == 1)
        b = "0" + b;

    return "#" + r + g + b;
}