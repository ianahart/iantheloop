

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

export function configureFormData(state, form) {

     const data =  state[form].map(({ field, value, errors }) => {

        return {
             [field]: value,
             errors,
          };
      })

      return Object.assign({}, ...data);
}


