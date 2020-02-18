import getInfo from './info-view.js';
import sendRequest from './reserve.js';

getInfo();

const formSubmitButton = document.querySelector('.intro #submit-button');
const formBudgetedExpenseYes = document.querySelector('.intro #budgeted-expense-yes');
const formBudgetedExpenseNo = document.querySelector('.intro #budgeted-expense-no');
const formOfficerName = document.querySelector('.intro #officerName');
const formOfficerPosition = document.querySelector('.intro #officerPosition');
const formDirectDeposit = document.querySelector('.intro #directDeposit');
const formCheck = document.querySelector('.intro #check');
const formAddress = document.querySelector('.intro #address');

receiptImage.onchange = function receiptImageOnChange() {
  let fileName = '';
  fileName = this.files[0].name;
  receiptImage.textContent = fileName;
};

documentsFile.onchange = function documentsFileOnChange() {
  let fileName = '';
  fileName = this.files[0].name;
  documentsFile.textContent = fileName;
};

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

formSubmitButton.onclick = function formSubmitButtonOnclick() {
  sendRequest(
    username,
    position,
    userEmail,
    userId,
    appDate,
    appVendor,
    appAmount,
    appDescription,
    officerName,
    officerPosition,
    directDeposit,
    check,
    receiptImage,
    documentsFile,
    address
  );
};