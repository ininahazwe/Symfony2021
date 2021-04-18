/**
 * Sets the cursor at the end of an editable element
 * @param {HTMLElement} element the editable element.
 */

export default function setCursorAtEnd(element){
    const range = document.createRange();
    const selection = window.getSelection();
    range.setStart(element, 1);
    range.collapse(true);
    selection.removeAllRanges();
    selection.addRange(range);
}