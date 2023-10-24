import React, { useState, useEffect } from 'react';
import PropTypes from 'prop-types';
import {  Link, Navigate } from 'react-router-dom';
import { Button, Form } from 'react-bootstrap';
import ReCAPTCHA from 'react-google-recaptcha';

import HabilitationService from 'services/habilitationsService';
import ConfigurationService from 'services/configurationService';

import {Axios} from 'helpers/axios';

const LoginForm = ({ hasLabel }) => {
  // State
  const [identifiant, setIdentifiant] = useState("");
  const [motDePasse, setMotDePasse] = useState("");
  const [mfa, setMfa] = useState("");
  const [reCaptchaToken, setReCaptchaToken] = useState("");

  const [loginStatus, setLoginStatus] = useState(false);
  const [redirectToChgPwd, setRedirectToChgPwd] = useState(false);
  const [redirectToCGU, setRedirectToCGU] = useState(false);
  const [erreurDeConnexion, setErreurDeConnexion] = useState(false);
  const [showFMA, setShowMFA] = useState(false);

  const [isLoading, setIsLoading] = useState(false);

  // Handler
  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      setIsLoading(true);
      const response = await Axios.post('login', {
        identifiant: identifiant,
        motDePasse: motDePasse,
        mfa: mfa,
        reCaptchaToken: reCaptchaToken,
      });

      if(!response.data.auth==true) {
        setLoginStatus(false);
        setRedirectToChgPwd(false);
        setErreurDeConnexion(true);
        setIsLoading(false);
      }else{
        HabilitationService.setToken(response.data.token);
        HabilitationService.setTokenValidUntil(response.data.tokenValidUntil);
        HabilitationService.setRefreshToken(response.data.refreshToken);
        HabilitationService.setHabilitations(response.data.habilitations);


        if(identifiant == motDePasse)
        {
          setRedirectToChgPwd(true);
        }
        else
        {
          if(response.data.disclaimerAccept == true)
          {
            setLoginStatus(true);
          }
          else
          {
            setRedirectToCGU(true);
          }
        }
        
      }

    } catch (e) {
      setErreurDeConnexion(true);
      setIsLoading(false);
      console.log(e);
    }
  };

  const checkMFAneeded = async () => {
    try {
      setIsLoading(true);
      const response = await Axios.post('mfaNeeded', {
        identifiant: identifiant,
      });
      setShowMFA(response.data.mfaNeeded);
      setIsLoading(false);
    } catch (error) {
      console.log(error)
    }
  }

  return (
    <>
    <Form onSubmit={handleSubmit} className="mb-3">
      <Form.Group className="mb-3">
        {hasLabel && <Form.Label>Nom d'utilisateur</Form.Label>}
        <Form.Control
          placeholder={!hasLabel ? 'Identifiant' : ''}
          value={identifiant}
          name="identifiant"
          onChange={(e) => setIdentifiant(e.target.value)}
          onBlur={checkMFAneeded}
          type="text"
        />
      </Form.Group>

      <Form.Group className="mb-3">
        {hasLabel && <Form.Label>Mot de passe</Form.Label>}
        <Form.Control
          placeholder={!hasLabel ? 'Mot de passe' : ''}
          value={motDePasse}
          name="motDePasse"
          onChange={(e) => setMotDePasse(e.target.value)}
          type="password"
        />
      </Form.Group>

      {showFMA ? 
        <Form.Group className="mb-3">
          {hasLabel && <Form.Label>Code MFA</Form.Label>}
          <Form.Control
            placeholder={!hasLabel ? 'Code MFA' : ''}
            value={mfa}
            name="mfa"
            onChange={(e) => setMfa(e.target.value)}
            type="text"
          />
        </Form.Group>
      : null}

      {process.env.REACT_APP_RECAPTCHA_ENABLED === "1" ? (
        <ReCAPTCHA
          sitekey={process.env.REACT_APP_RECAPTCHA_SITEKEY}
          onChange={token => setReCaptchaToken(token)}
          onExpired={e => setReCaptchaToken("")}
        />
      ) : null}

      <Form.Group>
        {erreurDeConnexion && <><center>Echec de la connexion</center></>}
        <Button
          type="submit"
          color="primary"
          className="mt-3 w-100"
          disabled={!identifiant || !motDePasse || isLoading}
        >
          {isLoading ? 'Chargement ...' : 'Se connecter'}
        </Button>
      </Form.Group>
      
      {loginStatus && <Navigate replace to="/home" />}
      {redirectToChgPwd && <Navigate replace to="/changePwdAtLogin" />}
      {redirectToCGU && <Navigate replace to="/cguAtLogin" />}
    </Form>

    {ConfigurationService.config['resetPassword'] ?
      <center><Link to="/mdpOublie">Mot de passe oublié</Link></center>
    : null}
    </>
  );
};

LoginForm.propTypes = {
  hasLabel: PropTypes.bool
};

LoginForm.defaultProps = {
  hasLabel: false
};

export default LoginForm;
