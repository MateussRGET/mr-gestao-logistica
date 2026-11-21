document.addEventListener('DOMContentLoaded', () => {
 const holder=document.querySelector('#product-rows');
 const add=document.querySelector('#add-product');
 if (!holder || !add) return;
 const template=document.querySelector('#product-row-template');
 function addRow(){
  const node=template.content.cloneNode(true);
  node.querySelector('.remove-product').addEventListener('click',e=>e.target.closest('.product-row').remove());
  holder.appendChild(node);
 }
 add.addEventListener('click',addRow);
 addRow();
});
