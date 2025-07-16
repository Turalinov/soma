// eslint-disable-next-line no-undef
module.exports = {
  'env': {
    'browser': true,
    'es2021': true
  },
  'extends': 'eslint:recommended',
  'overrides': [],
  'parserOptions': {
    'ecmaVersion': 'latest',
    'sourceType': 'module'
  },
  'rules': {
    'indent': [
      'error',
      'space'
    ],
    'linebreak-style': [
      'error',
      'unix'
    ],
    'quotes': [
      'error',
      'single'
    ],
    'semi': [
      'error',
      'always'
    ],
    'no-unused-vars': [
      'warn'
    ]
  }
};
