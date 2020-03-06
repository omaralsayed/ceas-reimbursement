// import getInfo from './info-view.js';
import sendRequest from './reserve.js';

// getInfo();

const formBudgetedExpenseYes = document.querySelector('.intro #budgeted-expense-yes');
const formBudgetedExpenseNo = document.querySelector('.intro #budgeted-expense-no');
const formOfficerName = document.querySelector('.intro #officer-name');
const formOfficerPosition = document.querySelector('.intro #officer-position');
const formDirectDeposit = document.querySelector('.intro #direct-deposit');
const formCheck = document.querySelector('.intro #check');
const formAddress = document.querySelector('.intro #address');
const formReceiptImage = document.querySelector('.intro #receipt-image');
const formDocumentsFile = document.querySelector('.intro #supporting-documents');
const formSubmitButton = document.querySelector('.intro #submit-button');

const username = document.getElementById('name');
const position = document.getElementById('position');
const email = document.getElementById('email');
const userId = document.getElementById('mId');
const appDate = document.getElementById('date');
const appVendor = document.getElementById('vendor');
const appAmount = document.getElementById('amount');
const appDescription = document.getElementById('description');
const officerName = document.getElementById('officer-name');
const officerPosition = document.getElementById('officer-position');
const directDeposit = document.getElementById('direct-deposit');
const check = document.getElementById('check');
const address = document.getElementById('address');
const receiptImage = document.getElementById('receipt-image');
const supportingDocuments = document.getElementById('supporting-documents');

formBudgetedExpenseYes.onclick = function formBudgetedExpenseYesButtonOnclick() {
  formOfficerName.style.visibility = 'hidden';
  formOfficerPosition.style.visibility = 'hidden';
  formBudgetedExpenseNo.checked = false;
  formOfficerName.style.height = '0';
  formOfficerName.style.overflow = 'hidden';
  formOfficerName.style.opacity = '0';
  formOfficerName.style.margin = '-10px';
  formOfficerName.style.padding = '0';
  formOfficerPosition.style.height = '0';
  formOfficerPosition.style.overflow = 'hidden';
  formOfficerPosition.style.opacity = '0';
  formOfficerPosition.style.margin = '-10px';
  formOfficerPosition.style.padding = '0';
};

formBudgetedExpenseNo.onclick = function formBudgetedExpenseNoButtonOnclick() {
  formOfficerName.style.visibility = 'visible';
  formOfficerPosition.style.visibility = 'visible';
  formBudgetedExpenseYes.checked = false;
  formOfficerName.style.height = '55px';
  formOfficerName.style.overflow = 'visible';
  formOfficerName.style.opacity = '1';
  formOfficerName.style.margin = '8px auto';
  formOfficerName.style.padding = '12px 0px';
  formOfficerPosition.style.height = '55px';
  formOfficerPosition.style.overflow = 'visible';
  formOfficerPosition.style.opacity = '1';
  formOfficerPosition.style.margin = '8px auto';
  formOfficerPosition.style.padding = '12px 0px';
};

formDirectDeposit.onclick = function formBudgetedExpenseYesButtonOnclick() {
  formAddress.style.visibility = 'hidden';
  formCheck.checked = false;
  formAddress.style.height = '0';
  formAddress.style.overflow = 'hidden';
  formAddress.style.opacity = '0';
  formAddress.style.margin = '0';
  formAddress.style.padding = '0';
};

formCheck.onclick = function formBudgetedExpenseNoButtonOnclick() {
  formAddress.style.visibility = 'visible';
  formDirectDeposit.checked = false;
  formAddress.style.height = '55px';
  formAddress.style.overflow = 'visible';
  formAddress.style.opacity = '1';
  formAddress.style.margin = '8px auto';
  formAddress.style.padding = '12px 0px';
};

formReceiptImage.onchange = function receiptImageOnChange() {
  let fileName = '';
  fileName = this.files[0].name;
  formReceiptImage.textContent = fileName;
};

formDocumentsFile.onchange = function documentsFileOnChange() {
  let fileName = '';
  fileName = this.files[0].name;
  formDocumentsFile.textContent = fileName;
};

formSubmitButton.onclick = function formSubmitButtonOnclick() {
  sendRequest(username, position, email, userId, appDate, appVendor, appAmount,
    appDescription, officerName, officerPosition, directDeposit, check, receiptImage, supportingDocuments, address);
};
