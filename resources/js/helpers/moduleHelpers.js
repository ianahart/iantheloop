

export function updateVisibility (state, form, isPasswordShowing) {

 return state[form].map(
          (oldField) => {

            const createPasswordShowing = oldField.field === 'password' && isPasswordShowing;

            const confirmPasswordShowing =  oldField.field === 'password_confirmation' && isPasswordShowing;

            if (
                 oldField.field.includes('password') ||
                 oldField.field.includes('password_confirmation')
            ) {
                 oldField.type = createPasswordShowing || confirmPasswordShowing ? 'text' : 'password';
                 return oldField;
              }
                return oldField;
          }
       );
}


// for single form
export function getFormData (state) {

     let input;

     const data =  state.form.map(({ field, value, defaultValue }) => {

          if (defaultValue !== undefined && defaultValue === value) {

               input = '';
          } else {

               input = value;
          }


        return {[field]: input};
      })

     return Object.assign({}, ...data);


}

// for module with more than one form
export function configureFormData(state, form) {

     const data =  state[form].map(({ field, value, errors }) => {

        return {
             [field]: value,
             errors,
          };
      })

      return Object.assign({}, ...data);
}

export function inputChange(state, payload) {

          const form = payload.form !== null ? state[payload.form] : state.form;

          form.find((oldField) => {

               if (oldField.field === payload.field) {

                    oldField.value = payload.value;

                    oldField.errors.push(payload.error);

                    state.hasErrors = oldField.errors.length ? true : false;
               }
          }
     );
}

export function pluckField (state, target) {

     return state.form.find((field) => field.field === target);
}

/**For Create Profile Forms**/
function hasErrors (form) {

     return form.some((field) => {

         return field.errors.length > 0;
     });
}

export function errorsPresent(form, name) {

     let errors;

     switch(name) {

          case 'generalDetails':
                errors = hasErrors(form);
                break;

          case 'aboutDetails':
               errors = hasErrors(form);
               break;

          case 'identity':
               hasErrors = false;
               break;

          default: ''
     }

     return errors;

}

export function clearFields (form) {

     form.forEach((field) => {

          field.value = '';

          field.interests = [];

     });
}

export function setCustomizeSrc (form, input, src) {

     form.forEach((field) => {

          if (field.field === input) {

               field.value = src;
          }
     });
}

export function retrieveFile (files, name) {

     const filtered = files.filter((file) => {

          if (file.input === name) {

               return file;
          }
     });

     return Object.assign({}, ...filtered);
}

export function filterEditGroup(form, group) {

    const filterGroup = {};

    const fields = form.filter((field) => field.group === group);
    fields.forEach((field) => {

     filterGroup[field.field] = field;
    });
    return filterGroup;
}

export function getCurrentRadioValue(form, targetField) {

     const radioField = form.find((field) => field.field === targetField);

     return radioField.value;
}

export function getObjPos(form, targetField) {

     return form.findIndex((field) => field.field === targetField);
}

// profileEdit.js
export function prepareEditFile(form, files, target) {

   const file = files.find((file) => file.input === target);
   const field = form.find((field) => field.field === target);
   let value = file;

   if (value.file === null) {

     value.src = field.value;
   }

   return value;
}

export function getElementIndex(container, elementProp, target) {

     return container.findIndex(element => element[elementProp] === target );
}

export function decodeToken(token) {
   const base64Url = token.split('.')[1];
   return base64Url.replace('-', '+').replace('_', '/');
}