export const POST_APPLICATION = 'POST_APPLICATION';
export const APPLICATION_CREATED = 'APPLICATION_CREATED';

export const postApplication = (application) => ({
  type: POST_APPLICATION,
  payload: application
});

export const applicationCreated = (application) => ({
  type: APPLICATION_CREATED,
  payload: application
});


