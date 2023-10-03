import React from 'react';
import AuthPwdChangeLayout from 'layouts/AuthPwdChangeLayout';
import Password from 'pages/internalApp/settings/utilisateurs/password';

const LoginPwdChange = () => {
    return (
		<AuthPwdChangeLayout>
			<Password checkOldPassword={false} layout="flat" redirectTarget="/" />
		</AuthPwdChangeLayout>
  );
};

export default LoginPwdChange;
