import displayWarning from './warning-view.js';

/**
 * Checks if a field is missing and highlights the field is so.
 * @param {Element} field - The field to check.
 * @returns {boolean} If the field is missing.
 */
const checkField = function checkForMissingField(field) {
  let isFieldMissing = false;

  if (field.value === '') {
    field.classList.add('missing');
    isFieldMissing = true;
  } else if (field.classList.contains('missing')) {
    field.classList.remove('missing');
  }

  return isFieldMissing;
};

/**
 * Validates data by checking for missing fields.
 * @param {Element} name - The name field to validate.
 * @param {Element} position - The position field to validate.
 * @param {Element} email - The email field to validate.
 * @param {Element} mId - The mId field to validate.
 * @param {Element} date - The date field to validate.
 * @param {Element} vendor - The vendor field to validate.
 * @param {Element} amount - The amount field to validate.
 * @param {Element} description - The description field to validate.
 * @returns {boolean} If any of the fields are missing.
 */
const validateData = function validateDataForMissingValues(name, position, email, mId, date, vendor, amount, description) {
  let isFieldMissing = false;
  const missingItems = [];

  missingItems.push(checkField(name));
  missingItems.push(checkField(position));
  missingItems.push(checkField(email));
  missingItems.push(checkField(mId));
  missingItems.push(checkField(date));
  missingItems.push(checkField(vendor));
  missingItems.push(checkField(amount));
  missingItems.push(checkField(description));

  if (missingItems.includes(true)) {
    isFieldMissing = true;
    displayWarning('Please fill in all of the fields.');
  }

  return isFieldMissing;
};

/**
 * Sends the form information to the backend through a POST call.
 * @param {Element} name - The name field to validate and send.
 * @param {Element} position - The position field to validate and send.
 * @param {Element} email - The email field to validate and send.
 * @param {Element} mId - The mId field to validate and send.
 * @param {Element} date - The date field to validate and send.
 * @param {Element} vendor - The vendor field to validate and send.
 * @param {Element} amount - The amount field to validate and send.
 * @param {Element} description - The description field to validate and send.
 * @param {Element} budgeted - The budgeted field to validate and send.
 * @param {Element} direct - The direct field to validate and send.
 * @param {Element} receipt - The receipt field to validate and send.
 * @param {Element} docs - The docs field to validate and send.
 */
const sendRequest = function sendRequestData(name, position, email, mId, date, vendor, amount, description, reciept, supportingDocs) {
  let officerName = '';
  let officerPosition = '';
  let budgeted;
  let direct;

  const formSubmitButton = document.querySelector('.intro #submit-button');
  const formLoader = document.querySelector('.intro .loader');

  const formOfficerName = document.querySelector('.intro #officer-name');
  const formOfficerPosition = document.querySelector('.intro #officer-position');
  const formAddress = document.querySelector('.intro #address');

  const formReceipt = document.querySelector('.intro #receipt-image');
  const formSupport = document.querySelector('.intro #supporting-documents');

  formSubmitButton.style.display = 'none';
  formLoader.style.display = 'block';

  if (formOfficerName.style.visibility == 'hidden') {
    budgeted = 'Budgeted';
  } else {
    budgeted = 'Not budgeted';
    officerName = formOfficerName.value;
    officerPosition = formOfficerPosition.value;
  }

  if (formAddress.style.visibility == 'hidden') {
    direct = 'Direct Deposit';
  } else {
    direct = 'Mail';
  }

  const submissionData = {
    name: name.value,
    position: position.value,
    email: email.value,
    mId: mId.value,
    date: date.value,
    vendor: vendor.value,
    amount: amount.value,
    description: description.value,
    budgetedExpenseYes: budgeted,
    directDeposit: direct,
    officerName: officerName,
    officerPosition: officerPosition,
    address: formAddress.value,
    receipt: reciept,
    docs: supportingDocs
  };

  const submissionFormData = new FormData();

  Object.keys(submissionData).forEach((data) => {
    submissionFormData.append(data, submissionData[data]);
  });

  fetch('api/send_request.php', {
    method: 'POST',
    body: submissionFormData,
  })
    .then(response => response.json())
    .then((data) => {
      if (data.status === 'success') {
        document.querySelector('.intro .request-form').style.display = 'none';
        document.querySelector('.reserved-ticket').style.display = 'block';
        document.querySelector('.reserved-ticket .reserved-email').textContent = email.value;
      } else {
        displayWarning(data.message);
        formLoader.style.display = 'none';
        formSubmitButton.style.display = 'block';
      }
    })
    .catch(() => {
      displayWarning('Something went wrong while sending your information. Please check your network connection and try again.');
      formLoader.style.display = 'none';
      formSubmitButton.style.display = 'block';
    });
};

/**
 * Validates and sends form data.
 * @param {Element} name - The name field to validate and send.
 * @param {Element} position - The position field to validate and send.
 * @param {Element} email - The email field to validate and send.
 * @param {Element} mId - The mId field to validate and send.
 * @param {Element} date - The date field to validate and send.
 * @param {Element} vendor - The vendor field to validate and send.
 * @param {Element} amount - The amount field to validate and send.
 * @param {Element} description - The description field to validate and send.
 */
export default function reserveTicket(
  name,
  position,
  email,
  mId,
  date,
  vendor,
  amount,
  description,
) {
  const fileSizeLimitMB = 6;
  const fileSizeLimit = fileSizeLimitMB * 1024 * 1024;
  const formReceipt = document.querySelector('.intro #receipt-image');
  const formSupport = document.querySelector('.intro #supporting-documents');

  if (formReceipt.files === null || formReceipt.files.length === 0) {
    displayWarning('Please attach a reciept before submitting.');
  } else if (formReceipt.files[0].size > fileSizeLimit) {
    displayWarning(`Please choose a reciept under ${fileSizeLimitMB}MB.`);
  } else if (
    formSupport.files !== null && formSupport.files.length > 0
    && formSupport.files[0].size > fileSizeLimit
  ) {
    displayWarning(`Please choose supporting documents under ${fileSizeLimitMB}MB.`);
  } else {
    const isMissing = validateData(name, position, email, mId, date, vendor, amount, description);

    const recieptFile = formReceipt.files[0];
    let supportingFile = null;
    if (formSupport.files.length > 0) {
      [supportingFile] = formSupport.files;
    }

    if (!isMissing) {
      sendRequest(
        name,
        position,
        email,
        mId,
        date,
        vendor,
        amount,
        description,
        recieptFile,
        supportingFile
      );
    }
  }
}
