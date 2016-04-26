def enum(*sequential, **named):
    enums = dict(zip(sequential, range(len(sequential))), **named)
    return type('Enum', (), enums)

PersistEngines = enum('Memory', 'Sql')
TransactionType = enum('Credit', 'Cash')

assert(PersistEngines.Memory == 0)


PERSIST_ENGINE = PersistEngines.Memory
SUPPORTED_CURRENCIES = ['coins']
TRANSACTION_TYPE = TransactionType.Credit


class Logger:
    pass

class ErrorLogger(Logger):
    def err(self):
        pass

class WarnLogger(Logger):
    def warn(self):
        pass

class Engine:
    def __init__(self, currencies):
        pass

class MemoryEngine(Engine):
    def save(self):
        pass

class MysqlEngine(Engine):
    def save(self):
        pass

class Currency:
    @staticmethod
    def currencies():
        return ['coins']

class Session:
    def __init__(self, user_id, engine):
        assert(isinstance(engine, MemoryEngine))

class Wallet:
    pass

class CreditTransaction:
    def __init__(self, session, currencies, logger):
        self.logger = logger

    def deposit(self, amount):
        assert(isinstance(logger, WarnLogger))
        logger.warn()
        print "deposited %s" % (amount,)


import inject


def create_engine():
    switcher = {
        PersistEngines.Memory: MemoryEngine,
        PersistEngines.Sql: MysqlEngine
        }
    currencies = Currency.currencies()
    return switcher[PERSIST_ENGINE](currencies)

def create_logger():
    return WarnLogger()

def create_session():
    return Session(12, inject.instance(Engine))

def create_wallet():
    currencies = Currency.currencies()
    logger = inject.instance(Logger)
    session = inject.instance(Session)
    return CreditTransaction(session, currencies, logger)


def production(binder):
    binder.bind_to_provider(Engine, create_engine)
    binder.bind_to_provider(Logger, create_logger)
    binder.bind_to_provider(Session, create_session)
    binder.bind_to_provider(Wallet, create_wallet)


inject.configure(production)

logger = inject.instance(Logger)
logger.warn()

wallet = inject.instance(Wallet)
wallet.deposit(10)

