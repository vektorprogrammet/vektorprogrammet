export const POST_NEWSLETTER = 'POST_NEWSLETTER';
export const NEWSLETTER_CREATED = 'NEWSLETTER_CREATED';

export const postNewsletter = (newsletter) => ({
  type: POST_NEWSLETTER,
  payload: newsletter
});

export const newsletterCreated = (newsletter) => ({
  type: NEWSLETTER_CREATED,
  payload: newsletter
});

//TODO: FÅ DETTE TIL Å FUNGERE