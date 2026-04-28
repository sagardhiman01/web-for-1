/** Security middleware starter (non-wired by default). */
let helmet;
let cors;
let rateLimit;
try { helmet = require('helmet'); } catch (_) { helmet = null; }
try { cors = require('cors'); } catch (_) { cors = null; }
try { rateLimit = require('express-rate-limit'); } catch (_) { rateLimit = null; }
const noOp = (_req, _res, next) => next();
module.exports = {
  helmetMiddleware: helmet ? helmet() : noOp,
  corsMiddleware: cors ? cors({ origin: true, credentials: true }) : noOp,
  apiRateLimit: rateLimit ? rateLimit({ windowMs: 15 * 60 * 1000, max: 100, standardHeaders: true, legacyHeaders: false }) : noOp,
};
