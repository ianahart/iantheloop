import axios from "axios";

import {
    getFormData,
    inputChange,
    pluckField,
    clearFields,
    errorsPresent
} from "../../../helpers/moduleHelpers.js";

const initialState = () => {
    return {
        form: [
            {
                field: "bio",
                errors: [],
                label: "Bio",
                value: "",
                size: "md",
                type: "text",
                nameAttr: "bio"
            },
            {
                field: "relationship",
                errors: [],
                label: "Relationship Status",
                value: "",
                size: "md",
                type: "radio",
                nameAttr: "relationship",
                statuses: [
                    "Single",
                    "In a relationship",
                    "Married",
                    "Divorced",
                    "N/A"
                ]
            },
            {
                field: "interests",
                errors: [],
                label: "Interests",
                value: "",
                size: "sm",
                type: "text",
                nameAttr: "interests",
                interests: []
            }
        ],
        interestsCounter: 0,
        errorsPresent: false,
        formName: "aboutDetails"
    };
};

const aboutDetails = {
    namespaced: true,

    state: initialState(),

    getters: {
        selectedRadio: state => {
            const radioField = state.form.find(
                field => field.field === "relationship"
            );

            return radioField.value;
        },

        getAboutDetails(state) {
            let prop;

            const data = state.form.map(({ field, value }, index) => {
                prop = value;

                if (field === "interests") {
                    prop = state.form[index].interests;
                }
                return { [field]: prop };
            });

            return Object.assign({}, ...data);
        },
        getBio(state) {
            return pluckField(state, "bio");
        },
        getRelationship(state) {
            return pluckField(state, "relationship");
        },

        getInterests(state) {
            return pluckField(state, "interests");
        }
    },

    mutations: {
        CLEAR_VALUES: state => {
            clearFields(state.form);
        },

        UPDATE_FIELD: (state, payload) => {
            inputChange(state, payload);
        },

        ADD_INTEREST: (state, payload) => {
            for (let i = 0; i < state.form.length; i++) {
                if (state.form[i].field === "interests") {
                    state.form[i].value = "";

                    if (state.form[i].interests.length < 5) {
                        state.interestsCounter++;

                        state.form[i].interests.push({
                            name: payload,
                            id: state.interestsCounter
                        });
                    }
                }
            }
        },

        REMOVE_INTEREST: (state, payload) => {
            let interests;

            for (let i = 0; i < state.form.length; i++) {
                if (state.form[i].field === "interests") {
                    interests = state.form[i].interests;

                    for (let j = 0; j < interests.length; j++) {
                        if (interests[j].id === payload) {
                            interests.splice(
                                interests.indexOf(interests[j]),
                                1
                            );
                        }
                    }
                }
            }
        },

        INTEREST_INPUT_CHANGE: (state, payload) => {
            for (let i = 0; i < state.form.length; i++) {
                if (state.form[i].field === "interests") {
                    state.form[i].value = payload;
                }
            }
        },

        RESET_MODULE: state => {
            Object.assign(state, initialState());
        },

        CLEAR_ERROR_MSGS: state => {
            state.form.forEach(field => {
                field.errors = [];
            });

            state.errorsPresent = false;
        },
        SET_ERRORS: (state, payload) => {
            state.form.forEach((input, fIdx) => {
                payload.forEach((error, pIdx) => {
                    const key = Object.keys(error);

                    if (input.field === key.toString()) {
                        if (
                            !state.form[fIdx].errors.includes(
                                ...payload[pIdx][input.field]
                            )
                        ) {
                            state.form[fIdx].errors.push(
                                ...payload[pIdx][input.field]
                            );
                        }
                    }
                });
            });

            if (errorsPresent(state.form, state.formName)) {
                state.errorsPresent = true;
            }
        }
    },

    actions: {}
};

export default aboutDetails;
